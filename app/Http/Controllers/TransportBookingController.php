<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\TransportBooking;
use App\Models\User;
use App\Models\Setting;
use App\Services\TransportBookingService;
use App\Mail\DispatchCardMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransportBookingController extends Controller
{
    protected $bookingService;

    public function __construct(TransportBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    protected function getUnifiedDrivers()
    {
        $externalNames = TransportBooking::whereNotNull('delivery_agent_name')
            ->where('delivery_agent_name', '!=', '')
            ->distinct()
            ->orderBy('delivery_agent_name')
            ->pluck('delivery_agent_name')
            ->filter(function ($name) {
                return !empty(trim($name));
            })
            ->values();

        return $externalNames->map(function ($name) {
            return [
                'id' => $name,
                'name' => $name,
                'type' => 'Outside Driver'
            ];
        });
    }

    public static function getExcludedCustomerIds()
    {
        return Customer::where(function ($q) {
            $q->where('name', 'like', '%CASH%')
              ->orWhere('name', 'like', '%ARUMUGAM%')
              ->orWhere('name', 'like', '%SRIVARSAN%');
        })->pluck('id')->toArray();
    }

    public function index(Request $request)
    {
        if (auth()->id() != 1 && (!auth()->user() || !auth()->user()->can('view transport-handover'))) {
            return unauthorizedRedirect();
        }

        $excludedCustomerIds = self::getExcludedCustomerIds();

        if ($request->ajax()) {
            $query = TransportBooking::with(['salesInvoice', 'customer.city', 'customer.place', 'deliveryAgent'])->orderBy('sales_invoice_id', 'desc')->orderBy('id', 'asc');

            if (!empty($excludedCustomerIds)) {
                $query->whereNotIn('customer_id', $excludedCustomerIds);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('lr_status')) {
                if ($request->lr_status === 'pending') {
                    $query->where(function ($q) {
                        $q->whereNull('lr_no')->orWhere('lr_no', '');
                    });
                } elseif ($request->lr_status === 'booked') {
                    $query->whereNotNull('lr_no')->where('lr_no', '!=', '');
                }
            }

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->filled('driver_id') || $request->filled('driver')) {
                $driverVal = $request->driver_id ?: $request->driver;
                if (is_numeric($driverVal)) {
                    $query->where(function ($q) use ($driverVal) {
                        $q->where('delivery_agent_id', $driverVal)
                          ->orWhere('delivery_agent_name', $driverVal);
                    });
                } else {
                    $cleanDriver = preg_replace('/\s*\((Outside Driver|Staff Driver)\)$/i', '', $driverVal);
                    $query->where('delivery_agent_name', trim($cleanDriver));
                }
            }

            if ($request->filled('transport_name')) {
                $query->where('preferred_transport', 'like', "%{$request->transport_name}%");
            }

            if ($request->filled('date')) {
                $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
                $query->where(function ($q) use ($date) {
                    $q->whereDate('booking_date', $date)
                      ->orWhere(function ($sub) use ($date) {
                          $sub->whereNull('booking_date')
                              ->whereHas('salesInvoice', function ($si) use ($date) {
                                  $si->whereDate('tran_doc_date', $date)
                                     ->orWhere(function ($s2) use ($date) {
                                         $s2->whereNull('tran_doc_date')
                                            ->whereDate('inv_date', $date);
                                     });
                              });
                      });
                });
            }

            $searchValue = trim($request->input('search.value') ?? (is_array($request->search) ? ($request->search['value'] ?? '') : (string)$request->input('search', '')));
            if ($searchValue !== '') {
                $cleanBox = trim(preg_replace('/^box\s*/i', '', $searchValue));
                $numericSearch = str_replace([',', '₹', 'Rs.', ' '], '', $searchValue);

                $query->where(function ($q) use ($searchValue, $cleanBox, $numericSearch) {
                    $q->where('lr_no', 'like', "%{$searchValue}%")
                        ->orWhere('preferred_transport', 'like', "%{$searchValue}%")
                        ->orWhere('booking_office', 'like', "%{$searchValue}%")
                        ->orWhere('box_number', 'like', "%{$searchValue}%")
                        ->orWhere('status', 'like', "%{$searchValue}%")
                        ->orWhere('delivery_agent_name', 'like', "%{$searchValue}%");

                    if ($cleanBox !== '' && $cleanBox !== $searchValue) {
                        $q->orWhere('box_number', 'like', "%{$cleanBox}%");
                    }

                    if (is_numeric($numericSearch)) {
                        $q->orWhere('quantity', $numericSearch)
                            ->orWhere('lr_price', 'like', "%{$numericSearch}%")
                            ->orWhere('booking_rate', 'like', "%{$numericSearch}%");
                    }

                    $q->orWhereHas('salesInvoice', function ($si) use ($searchValue) {
                        $si->where('inv_no', 'like', "%{$searchValue}%");
                        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $searchValue)) {
                            $dbDate = Carbon::createFromFormat('d-m-Y', $searchValue)->format('Y-m-d');
                            $si->orWhereDate('inv_date', $dbDate);
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchValue)) {
                            $si->orWhereDate('inv_date', $searchValue);
                        }
                    })
                    ->orWhereHas('customer', function ($c) use ($searchValue) {
                        $c->where('name', 'like', "%{$searchValue}%")
                            ->orWhere('code', 'like', "%{$searchValue}%")
                            ->orWhere('mobile_no', 'like', "%{$searchValue}%")
                            ->orWhere('alt_transport_name', 'like', "%{$searchValue}%")
                            ->orWhereHas('city', function ($ct) use ($searchValue) {
                                $ct->where('city_name', 'like', "%{$searchValue}%");
                            })
                            ->orWhereHas('place', function ($pl) use ($searchValue) {
                                $pl->where('place_name', 'like', "%{$searchValue}%");
                            });
                    })
                    ->orWhereHas('deliveryAgent', function ($da) use ($searchValue) {
                        $da->where('name', 'like', "%{$searchValue}%");
                    });
                });
            }

            $totalRecords = TransportBooking::count();
            $filteredRecords = $query->count();

            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);

            if ($limit != -1) {
                $query->skip($start)->take($limit);
            }

            $bookings = $query->get();
            $canEdit = (auth()->id() == 1 || auth()->user()->can('create transport-handover') || auth()->user()->can('edit transport-handover'));

            $data = [];
            foreach ($bookings as $idx => $b) {
                $custName = $b->customer ? $b->customer->name : 'N/A';
                $custCode = $b->customer ? $b->customer->code : '';
                $custEmail = $b->customer ? ($b->customer->email ?: ($b->customer->contact_email ?: '')) : '';
                $escapedCustEmail = htmlspecialchars($custEmail, ENT_QUOTES, 'UTF-8');
                $place = (!empty($b->booking_office) && $b->booking_office !== '-')
                    ? $b->booking_office
                    : ($b->customer && $b->customer->city ? $b->customer->city->city_name : ($b->customer && $b->customer->place ? $b->customer->place->place_name : ''));

                $placeDisplay = $place ? ($custCode ? "{$place} ({$custCode})" : $place) : ($custCode ? "({$custCode})" : '');

                $effDispDate = $b->booking_date 
                    ?: ($b->salesInvoice && $b->salesInvoice->tran_doc_date ? $b->salesInvoice->tran_doc_date : null);

                if ($b->status === 'Booked' && $effDispDate) {
                    $dispDate = Carbon::parse($effDispDate)->format('d-m-Y');
                    $dispBadge = "<span class='badge bg-label-info fw-bold font-monospace text-nowrap' style='font-size: 0.72rem; padding: 2px 6px;'><i class='ri ri-truck-line me-1'></i>Disp: {$dispDate}</span>";
                } elseif ($effDispDate) {
                    $dispDate = Carbon::parse($effDispDate)->format('d-m-Y');
                    $dispBadge = "<span class='badge bg-label-primary fw-medium font-monospace text-nowrap' style='font-size: 0.72rem; padding: 2px 6px;'><i class='ri ri-calendar-line me-1'></i>Disp: {$dispDate}</span>";
                } else {
                    $dispBadge = "<span class='badge bg-label-warning text-nowrap' style='font-size: 0.7rem; padding: 2px 6px;'><i class='ri ri-time-line me-1'></i>Pending Disp</span>";
                }

                $customerHtml = "<div>" .
                    "<span class='fw-bold text-dark d-block'>{$custName}</span>" .
                    "<div class='d-flex align-items-center gap-2 mt-1 flex-wrap'>" .
                        ($placeDisplay ? "<small class='text-muted'>{$placeDisplay}</small>" : "") .
                        $dispBadge .
                    "</div>" .
                "</div>";
                
                $invNo = $b->salesInvoice ? $b->salesInvoice->inv_no : '-';
                $invUrl = url('sales_invoices/view/' . $b->sales_invoice_id);
                $invHtml = "<a href='{$invUrl}' class='fw-semibold text-primary' target='_blank'>{$invNo}</a>";

                $invDate = ($b->salesInvoice && $b->salesInvoice->inv_date) ? Carbon::parse($b->salesInvoice->inv_date)->format('d-m-Y') : '-';
                $invDateHtml = "<span class='text-nowrap text-secondary fw-medium'>{$invDate}</span>";

                $dispatchDateHtml = $dispBadge;

                $boxHtml = "<span class='badge bg-label-dark fw-bold'>Box {$b->box_number}</span>";

                $driverName = $b->deliveryAgent ? $b->deliveryAgent->name : ($b->delivery_agent_name ?: '');
                if ($b->deliveryAgent) {
                    $driverHtml = "<span class='badge bg-label-primary'><i class='ri ri-user-line me-1'></i>{$b->deliveryAgent->name}</span>";
                } elseif (!empty($b->delivery_agent_name)) {
                    $driverHtml = "<span class='badge bg-label-info' title='Outside Delivery Agent'><i class='ri-user-received-line me-1'></i>" . e($b->delivery_agent_name) . " <span class='badge bg-white text-info ms-1' style='font-size: 0.65rem;'>Outside</span></span>";
                } else {
                    $driverHtml = "<span class='badge bg-label-secondary'>Unassigned</span>";
                }

                $statusHtml = '';
                if ($b->status === 'Booked') {
                    $statusHtml = "<span class='badge bg-label-success'><i class='ri ri-checkbox-circle-line me-1'></i> Booked</span>";
                } elseif ($b->status === 'Picked') {
                    $statusHtml = "<span class='badge bg-label-warning'><i class='ri ri-truck-line me-1'></i> Picked</span>";
                } else {
                    $statusHtml = "<span class='badge bg-label-secondary'>Ready for Delivery</span>";
                }

                if ($b->customer_notified) {
                    $statusHtml .= " <span class='badge bg-label-info ms-1' title='Customer Notified via Email'><i class='ri ri-mail-check-line'></i></span>";
                }

                $escapedDriverName = htmlspecialchars($driverName, ENT_QUOTES, 'UTF-8');
                $driverVal = $b->delivery_agent_id ?: ($b->delivery_agent_name ?: '');
                $bDate = $b->booking_date ? Carbon::parse($b->booking_date)->format('d-m-Y') : date('d-m-Y');

                $lrHtml = '';
                if (!empty($b->lr_no)) {
                    $lrHtml = "<span class='badge bg-label-success fw-bold font-monospace fs-7'>{$b->lr_no}</span>";
                } elseif ($canEdit) {
                    $lrHtml = "<button type='button' class='btn btn-xs btn-outline-danger btn-open-lr-modal' data-id='{$b->id}' data-invoice='{$invNo}' data-box='{$b->box_number}' data-customer='{$custName}' data-customer-email='{$escapedCustEmail}' data-transport='{$b->preferred_transport}' data-rate='{$b->booking_rate}' data-lr='' data-driver='{$driverVal}' data-driver-name='{$escapedDriverName}' data-booking-date='{$bDate}'>+ Enter LR</button>";
                } else {
                    $lrHtml = "<span class='badge bg-label-secondary'>Pending LR</span>";
                }

                $editBtn = '';
                if ($canEdit) {
                    $editBtn = "<button type='button' class='btn btn-icon btn-text-primary rounded-pill btn-open-lr-modal me-1' data-id='{$b->id}' data-invoice='{$invNo}' data-box='{$b->box_number}' data-customer='{$custName}' data-customer-email='{$escapedCustEmail}' data-transport='{$b->preferred_transport}' data-rate='{$b->booking_rate}' data-lr='{$b->lr_no}' data-driver='{$driverVal}' data-driver-name='{$escapedDriverName}' data-booking-date='{$bDate}' title='Update LR Details' style='width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;'><i class='ri ri-pencil-line' style='font-size: 22px;'></i></button>";
                }
                $dispatchPdfBtn = "<a href='" . route('transport_bookings.dispatch_card_pdf', $b->id) . "' target='_blank' class='btn btn-icon btn-text-danger rounded-pill' title='View / Download Dispatch Slip PDF' style='width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;'><i class='ri ri-file-pdf-line' style='font-size: 22px;'></i></a>";

                $actionHtml = "<div class='d-flex align-items-center'>{$editBtn}{$dispatchPdfBtn}</div>";

                if ($b->status === 'Booked' || !empty($b->lr_no)) {
                    $escapedLr = htmlspecialchars($b->lr_no ?: '', ENT_QUOTES, 'UTF-8');
                    $checkboxHtml = "<span class='d-inline-flex align-items-center justify-content-center' title='Already Booked (LR: {$escapedLr}) - Handover Completed'><i class='ri ri-checkbox-circle-fill text-success' style='font-size: 19px;'></i></span>";
                } else {
                    $checkboxHtml = "<input type='checkbox' class='form-check-input row-booking-checkbox' value='{$b->id}' data-transport='" . htmlspecialchars($b->preferred_transport ?: '', ENT_QUOTES, 'UTF-8') . "' title='Select for Handover Sheet'>";
                }

                $data[] = [
                    'checkbox' => $checkboxHtml,
                    'DT_RowIndex' => $start + $idx + 1,
                    'inv_no' => $invHtml,
                    'inv_date' => $invDateHtml,
                    'dispatch_date' => $dispatchDateHtml,
                    'customer' => $customerHtml,
                    'box_number' => $boxHtml,
                    'quantity' => (int)$b->quantity,
                    'transport_name' => $b->preferred_transport ?: '-',
                    'driver' => $driverHtml,
                    'lr_no' => $lrHtml,
                    'freight_price' => '₹' . number_format($b->lr_price ?: $b->booking_rate, 2),
                    'status' => $statusHtml,
                    'action' => $actionHtml,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        }

        $customers = Customer::where('status', 'Active')
            ->when(!empty($excludedCustomerIds), function ($q) use ($excludedCustomerIds) {
                $q->whereNotIn('id', $excludedCustomerIds);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $drivers = $this->getUnifiedDrivers();
        $transporters = TransportBooking::whereNotNull('preferred_transport')
            ->where('preferred_transport', '!=', '')
            ->where('preferred_transport', '!=', 'None')
            ->where('preferred_transport', '!=', '-')
            ->when(!empty($excludedCustomerIds), function ($q) use ($excludedCustomerIds) {
                $q->whereNotIn('customer_id', $excludedCustomerIds);
            })
            ->distinct()
            ->orderBy('preferred_transport')
            ->pluck('preferred_transport');

        $totalBoxesCount = TransportBooking::when(!empty($excludedCustomerIds), function ($q) use ($excludedCustomerIds) {
            $q->whereNotIn('customer_id', $excludedCustomerIds);
        })->count();

        $bookedBoxesCount = TransportBooking::when(!empty($excludedCustomerIds), function ($q) use ($excludedCustomerIds) {
            $q->whereNotIn('customer_id', $excludedCustomerIds);
        })->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->count();

        $pendingLrCount = TransportBooking::when(!empty($excludedCustomerIds), function ($q) use ($excludedCustomerIds) {
            $q->whereNotIn('customer_id', $excludedCustomerIds);
        })->where(function ($q) {
            $q->whereNull('lr_no')->orWhere('lr_no', '');
        })->count();

        return view('transport_bookings.index', compact(
            'customers',
            'drivers',
            'transporters',
            'totalBoxesCount',
            'bookedBoxesCount',
            'pendingLrCount'
        ));
    }

    public function recordLr(Request $request, $id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create transport-handover') && !auth()->user()->can('edit transport-handover')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action. You do not have permission to record LR details.'], 403);
        }

        $validated = $request->validate([
            'lr_no' => 'required|string',
            'lr_price' => 'nullable|numeric|min:0',
            'preferred_transport' => 'nullable|string',
            'delivery_agent_id' => 'nullable|string',
            'delivery_agent_name' => 'nullable|string',
            'booking_date' => 'nullable|string',
            'remarks' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'box_number' => 'nullable|string',
        ]);

        try {
            $existingBooking = TransportBooking::with(['salesInvoice', 'customer'])->find($id);
            $oldData = $existingBooking ? [
                'lr_no' => $existingBooking->lr_no,
                'lr_price' => $existingBooking->lr_price,
                'status' => $existingBooking->status,
                'preferred_transport' => $existingBooking->preferred_transport,
                'delivery_agent' => $existingBooking->delivery_agent_name ?: ($existingBooking->deliveryAgent->name ?? ''),
                'booking_date' => $existingBooking->booking_date ? Carbon::parse($existingBooking->booking_date)->format('d-m-Y') : null,
            ] : null;

            $agentInput = trim((string)($request->input('delivery_agent_id') ?: $request->input('delivery_agent_name')));
            $driverId = null;
            $driverName = null;

            if (!empty($agentInput)) {
                $agentInput = preg_replace('/\s*\((New Outside Driver|Outside Driver|Staff Driver|Outside)\)$/i', '', $agentInput);
                $agentInput = trim($agentInput);

                if (is_numeric($agentInput)) {
                    $user = User::find($agentInput);
                    if ($user) {
                        $driverId = $user->id;
                        $driverName = $user->name;
                    } else {
                        $driverName = $agentInput;
                    }
                } else {
                    $driverName = $agentInput;
                }
            }

            $validated['delivery_agent_id'] = $driverId;
            $validated['delivery_agent_name'] = $driverName;
            $validated['status'] = 'Booked';

            $booking = $this->bookingService->recordLrBooking($id, $validated);

            if (function_exists('addLog')) {
                $booking->loadMissing(['salesInvoice', 'customer', 'deliveryAgent']);
                $invNo = $booking->salesInvoice ? $booking->salesInvoice->inv_no : "ID #{$booking->sales_invoice_id}";
                $driverText = $booking->delivery_agent_name ?: ($booking->deliveryAgent->name ?? 'Unassigned');
                $actionType = empty($oldData['lr_no']) ? 'create_lr' : 'update_lr';
                $newData = [
                    'lr_no' => $booking->lr_no,
                    'lr_price' => $booking->lr_price,
                    'status' => $booking->status,
                    'preferred_transport' => $booking->preferred_transport,
                    'delivery_agent' => $driverText,
                    'booking_date' => $booking->booking_date ? Carbon::parse($booking->booking_date)->format('d-m-Y') : null,
                ];
                $desc = "LR {$booking->lr_no} recorded for Invoice {$invNo} (Box {$booking->box_number}) - Transporter: {$booking->preferred_transport}, Driver: {$driverText}";

                addLog($actionType, 'Transport Handover', 'transport_bookings', $booking->id, $oldData, $newData, $desc);
            }

            $targetEmail = trim((string)$request->input('customer_email'));
            if (!empty($targetEmail)) {
                $booking->loadMissing('customer');
                if ($booking->customer && (empty($booking->customer->email) || $booking->customer->email !== $targetEmail)) {
                    $booking->customer->email = $targetEmail;
                    $booking->customer->save();
                }
            }

            $emailResult = $this->sendDispatchCardEmail($booking, $targetEmail ?: null);

            $mailMsg = '';
            if ($emailResult['sent']) {
                $mailMsg = " & Customer dispatch email sent to {$emailResult['email']}.";
            } elseif ($emailResult['reason'] === 'No customer email address on file') {
                $mailMsg = " (Notice: Customer has no email registered, dispatch email skipped).";
            } else {
                $mailMsg = " (Email status: {$emailResult['reason']}).";
            }

            return response()->json([
                'success' => true,
                'message' => 'LR ' . $booking->lr_no . ' saved successfully! Status updated to Booked' . $mailMsg,
                'booking' => $booking
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markPicked(Request $request, $id)
    {
        $booking = TransportBooking::findOrFail($id);
        $booking->picked_by_agent = true;
        $booking->picked_at = Carbon::now();
        $booking->status = 'Picked';

        $agentInput = trim((string)($request->input('driver_id') ?: $request->input('delivery_agent_name')));
        if (!empty($agentInput)) {
            $agentInput = preg_replace('/\s*\((New Outside Driver|Outside Driver|Staff Driver|Outside)\)$/i', '', $agentInput);
            $agentInput = trim($agentInput);
            if (is_numeric($agentInput)) {
                $driver = User::find($agentInput);
                $booking->delivery_agent_id = $driver ? $driver->id : null;
                $booking->delivery_agent_name = $driver ? $driver->name : $agentInput;
            } else {
                $booking->delivery_agent_id = null;
                $booking->delivery_agent_name = $agentInput;
            }
        }
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => "Box {$booking->box_number} marked as Picked."
        ]);
    }

    public function printSheet(Request $request)
    {
        if (auth()->id() != 1 && (!auth()->user() || !auth()->user()->can('view transport-handover'))) {
            return unauthorizedRedirect();
        }

        $setting = Setting::first();
        $excludedCustomerIds = self::getExcludedCustomerIds();

        $query = TransportBooking::with(['salesInvoice', 'customer.city', 'customer.place', 'deliveryAgent']);

        if (!empty($excludedCustomerIds)) {
            $query->whereNotIn('customer_id', $excludedCustomerIds);
        }

        $selectedBookingIds = [];
        if ($request->filled('booking_ids')) {
            $ids = is_array($request->booking_ids) ? $request->booking_ids : explode(',', $request->booking_ids);
            $selectedBookingIds = array_filter(array_map('trim', $ids));
        }

        if (!empty($selectedBookingIds)) {
            $query->whereIn('id', $selectedBookingIds);
            $sheetDate = date('d-m-Y');
            $title = "Transport Handover Sheet (" . count($selectedBookingIds) . " Boxes) - " . $sheetDate;
        } elseif ($request->filled('invoice_id')) {
            $query->where('sales_invoice_id', $request->invoice_id);
            $title = "Transport Handover Sheet - Invoice #" . $request->invoice_id;
            $sheetDate = date('d-m-Y');
        } elseif ($request->filled('date')) {
            $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            $query->where(function ($q) use ($date) {
                $q->whereHas('salesInvoice', function ($si) use ($date) {
                    $si->whereDate('inv_date', $date);
                })->orWhereDate('created_at', $date);
            });
            if (!$request->filled('lr_status')) {
                $query->where('status', '!=', 'Booked');
            }
            $title = "Transport Handover Sheet - " . $request->date;
            $sheetDate = $request->date;
        } else {
            $today = Carbon::today()->format('Y-m-d');
            $todayCount = TransportBooking::when(!empty($excludedCustomerIds), function ($q) use ($excludedCustomerIds) {
                $q->whereNotIn('customer_id', $excludedCustomerIds);
            })->where(function ($q) use ($today) {
                $q->whereHas('salesInvoice', function ($si) use ($today) {
                    $si->whereDate('inv_date', $today);
                })->orWhereDate('created_at', $today);
            })->count();

            if ($todayCount > 0) {
                $query->where(function ($q) use ($today) {
                    $q->whereHas('salesInvoice', function ($si) use ($today) {
                        $si->whereDate('inv_date', $today);
                    })->orWhereDate('created_at', $today);
                });
                if (!$request->filled('lr_status')) {
                    $query->where('status', '!=', 'Booked');
                }
                $title = "Today's Transport Handover Sheet - " . date('d-m-Y');
                $sheetDate = date('d-m-Y');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('lr_no')->orWhere('lr_no', '');
                });
                $title = "Transport Handover Sheet (Pending Handover) - " . date('d-m-Y');
                $sheetDate = date('d-m-Y');
            }
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('driver_id') || $request->filled('driver')) {
            $driverVal = $request->driver_id ?: $request->driver;
            if (is_numeric($driverVal)) {
                $query->where(function ($q) use ($driverVal) {
                    $q->where('delivery_agent_id', $driverVal)
                      ->orWhere('delivery_agent_name', $driverVal);
                });
            } else {
                $cleanDriver = preg_replace('/\s*\((Outside Driver|Staff Driver|Outside)\)$/i', '', $driverVal);
                $query->where('delivery_agent_name', trim($cleanDriver));
            }
        }

        if ($request->filled('transport_name') && !empty($request->transport_name)) {
            $query->where('preferred_transport', 'like', "%{$request->transport_name}%");
            $title = strtoupper($request->transport_name) . " Transport Handover Sheet - " . $sheetDate;
        }

        if ($request->filled('lr_status')) {
            if ($request->lr_status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('lr_no')->orWhere('lr_no', '');
                });
            } elseif ($request->lr_status === 'booked') {
                $query->whereNotNull('lr_no')->where('lr_no', '!=', '');
            }
        }

        if (!empty($selectedBookingIds)) {
            $bookings = $query->orderBy('sales_invoice_id', 'desc')->get();
        } else {
            $bookings = $query->orderBy('sales_invoice_id', 'desc')->take(100)->get();
        }

        $pdf = Pdf::loadView('transport_bookings.sheet_pdf', [
            'bookings' => $bookings,
            'setting' => $setting,
            'title' => $title,
            'date' => $sheetDate
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Handover_Sheet_' . date('Ymd_His') . '.pdf');
    }

    public function paymentClearance(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('clearance transport-handover')) {
            return unauthorizedRedirect();
        }

        $drivers = $this->getUnifiedDrivers();
        $excludedCustomerIds = self::getExcludedCustomerIds();

        $startDate = $request->filled('start_date') ? Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedDriverId = $request->driver_id;

        $baseQuery = TransportBooking::with(['salesInvoice', 'customer', 'deliveryAgent'])->whereBetween('created_at', [$startDate, $endDate]);

        if (!empty($excludedCustomerIds)) {
            $baseQuery->whereNotIn('customer_id', $excludedCustomerIds);
        }

        if (!empty($selectedDriverId)) {
            if (is_numeric($selectedDriverId)) {
                $baseQuery->where(function ($q) use ($selectedDriverId) {
                    $q->where('delivery_agent_id', $selectedDriverId)
                      ->orWhere('delivery_agent_name', $selectedDriverId);
                });
            } else {
                $cleanDriver = preg_replace('/\s*\((Outside Driver|Staff Driver|Outside)\)$/i', '', $selectedDriverId);
                $cleanDriver = trim($cleanDriver);
                $baseQuery->where('delivery_agent_name', $cleanDriver);
            }
        }

        if ($request->ajax() && $request->filled('table_type')) {
            $tableType = $request->table_type; 
            $query = clone $baseQuery;

            if ($tableType === 'eligible') {
                $query->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '');

                if ($request->filled('payout_status')) {
                    if ($request->payout_status === 'cleared') {
                        $query->where('payment_status', 'Cleared');
                    } elseif ($request->payout_status === 'pending') {
                        $query->where(function ($sq) {
                            $sq->whereNull('payment_status')->orWhere('payment_status', '!=', 'Cleared');
                        });
                    }
                }
            } else {
                $query->where(function ($q) {
                    $q->whereNull('lr_no')->orWhere('lr_no', '');
                });
            }

            $searchValue = trim($request->input('search.value', ''));
            if ($searchValue !== '') {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('lr_no', 'like', "%{$searchValue}%")
                        ->orWhere('preferred_transport', 'like', "%{$searchValue}%")
                        ->orWhere('box_number', 'like', "%{$searchValue}%")
                        ->orWhere('delivery_agent_name', 'like', "%{$searchValue}%")
                        ->orWhereHas('customer', function ($c) use ($searchValue) {
                            $c->where('name', 'like', "%{$searchValue}%")
                                ->orWhere('code', 'like', "%{$searchValue}%");
                        })
                        ->orWhereHas('salesInvoice', function ($si) use ($searchValue) {
                            $si->where('inv_no', 'like', "%{$searchValue}%");
                        });
                });
            }

            $totalRecords = (clone $baseQuery)->when($tableType === 'eligible', function($q) use ($request) {
                $q->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '');
                if ($request->filled('payout_status')) {
                    if ($request->payout_status === 'cleared') {
                        $q->where('payment_status', 'Cleared');
                    } elseif ($request->payout_status === 'pending') {
                        $q->where(function ($sq) {
                            $sq->whereNull('payment_status')->orWhere('payment_status', '!=', 'Cleared');
                        });
                    }
                }
            }, function($q) {
                $q->where(function ($sq) {
                    $sq->whereNull('lr_no')->orWhere('lr_no', '');
                });
            })->count();

            $filteredRecords = $query->count();

            $start = (int)$request->input('start', 0);
            $length = (int)$request->input('length', 10);
            if ($length !== -1) {
                $query->offset($start)->limit($length);
            }

            $rows = $query->orderBy('id', 'desc')->get();

            $data = [];
            foreach ($rows as $idx => $b) {
                $customerHtml = '<strong class="d-block text-dark">' . e(optional($b->customer)->name ?? '-') . '</strong>' .
                    '<small class="text-muted">' . e(optional($b->customer)->code ?? '') . '</small>';

                $invoiceUrl = url('sales_invoices/view/' . $b->sales_invoice_id);
                $invoiceHtml = '<a href="' . $invoiceUrl . '" target="_blank" class="fw-semibold text-primary">' .
                    e(optional($b->salesInvoice)->inv_no ?? '-') . '</a>';

                $boxHtml = '<span class="badge bg-dark fw-bold">' . e($b->box_number) . '</span>';
                $driverName = optional($b->deliveryAgent)->name ?? ($b->delivery_agent_name ?: '-');
                $isOutside = empty($b->delivery_agent_id) && !empty($b->delivery_agent_name);

                $driverBadge = $isOutside
                    ? '<span class="badge bg-label-info"><i class="ri ri-user-received-line me-1"></i>' . e($driverName) . ' <span class="badge bg-white text-info ms-1" style="font-size:0.65rem;">Outside</span></span>'
                    : (!empty($driverName) && $driverName !== '-'
                        ? '<span class="badge bg-label-primary"><i class="ri ri-user-line me-1"></i>' . e($driverName) . '</span>'
                        : '<span class="badge bg-label-secondary">Unassigned</span>');

                if ($tableType === 'eligible') {
                    $isCleared = $b->payment_status === 'Cleared';
                    $statusHtml = $isCleared
                        ? '<span class="badge bg-success">Cleared on ' . ($b->payment_cleared_date ? $b->payment_cleared_date->format('d-m-Y') : '') . '</span>'
                        : '<span class="badge bg-label-warning">Eligible (Pending Clearance)</span>';

                    $checkboxHtml = $isCleared
                        ? '<input type="checkbox" class="form-check-input" disabled title="Already Cleared">'
                        : '<input type="checkbox" class="form-check-input row-checkbox-eligible" value="' . $b->id . '" data-price="' . (float)$b->lr_price . '" checked>';

                    $data[] = [
                        'checkbox' => $checkboxHtml,
                        'sno' => $start + $idx + 1,
                        'customer' => $customerHtml,
                        'invoice_no' => $invoiceHtml,
                        'box' => $boxHtml,
                        'qty' => (int)$b->quantity,
                        'transport' => e($b->preferred_transport ?: '-'),
                        'driver' => $driverBadge,
                        'lr_number' => '<span class="badge bg-label-success fw-bold">' . e($b->lr_no) . '</span>',
                        'booking_date' => $b->booking_date ? $b->booking_date->format('d-m-Y') : '-',
                        'approved_price' => '<span class="text-end fw-bold text-success d-block">₹' . number_format($b->lr_price, 2) . '</span>',
                        'payment_status' => $statusHtml
                    ];
                } else {
                    $data[] = [
                        'sno' => $start + $idx + 1,
                        'customer' => $customerHtml,
                        'invoice_no' => $invoiceHtml,
                        'box' => '<span class="badge bg-secondary">' . e($b->box_number) . '</span>',
                        'qty' => (int)$b->quantity,
                        'transport' => e($b->preferred_transport ?: '-'),
                        'driver' => $driverBadge,
                        'status' => '<span class="badge bg-label-secondary">' . e($b->status) . '</span>',
                        'lr_status' => '<span class="badge bg-danger">NO LR NUMBER</span>',
                        'claim_amount' => '<span class="text-end fw-bold text-danger d-block">₹0.00 (LOCKED)</span>'
                    ];
                }
            }

            $eligibleCount = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->count();
            $eligibleSum = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->sum('lr_price');
            $blockedCount = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('lr_no')->orWhere('lr_no', '');
            })->count();

            return response()->json([
                'draw' => (int)$request->input('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
                'metrics' => [
                    'eligible_count' => $eligibleCount,
                    'eligible_amount' => number_format($eligibleSum, 2),
                    'blocked_count' => $blockedCount,
                ]
            ]);
        }

        if ($request->isMethod('post') && $request->action === 'clear_payment') {
            $clearQuery = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->where('payment_status', '!=', 'Cleared');

            if ($request->filled('selected_booking_ids')) {
                $ids = explode(',', $request->selected_booking_ids);
                $ids = array_filter(array_map('trim', $ids));
                if (!empty($ids)) {
                    $clearQuery->whereIn('id', $ids);
                }
            }

            $eligibleIds = $clearQuery->pluck('id');

            if ($eligibleIds->count() > 0) {
                TransportBooking::whereIn('id', $eligibleIds)->update([
                    'payment_status' => 'Cleared',
                    'payment_cleared_date' => Carbon::now(),
                    'updated_by' => auth()->id() ?? 1,
                ]);

                if (function_exists('addLog')) {
                    $totalClearedAmt = TransportBooking::whereIn('id', $eligibleIds)->sum('lr_price');
                    $driverNameText = $selectedDriverId ?: 'Multiple Drivers';
                    addLog(
                        'payment_clearance',
                        'Transport Handover',
                        'transport_bookings',
                        $eligibleIds->first(),
                        ['payment_status' => 'Unapproved'],
                        ['payment_status' => 'Cleared', 'cleared_count' => count($eligibleIds), 'total_amount' => $totalClearedAmt],
                        "Driver Weekly Payment Cleared for " . count($eligibleIds) . " parcel(s) (Total: ₹" . number_format($totalClearedAmt, 2) . ") for Driver: {$driverNameText}"
                    );
                }

                return redirect()->route('transport_bookings.payment_clearance', $request->only(['driver_id', 'start_date', 'end_date']))->with('success', 'Weekly payout of ' . count($eligibleIds) . ' selected boxes successfully cleared!');
            }

            return redirect()->route('transport_bookings.payment_clearance', $request->only(['driver_id', 'start_date', 'end_date']))
                ->with('error', 'No eligible boxes selected for payment clearance.');
        }

        $eligibleBookings = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->limit(10)->get();
        $blockedBookings = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('lr_no')->orWhere('lr_no', '');
            })->limit(10)->get();

        $eligibleCount = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->count();
        $eligibleAmount = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->sum('lr_price');
        $blockedCount = (clone $baseQuery)->where(function ($q) {
            $q->whereNull('lr_no')->orWhere('lr_no', '');
        })->count();

        return view('transport_bookings.payment_clearance', compact(
            'drivers',
            'selectedDriverId',
            'startDate',
            'endDate',
            'eligibleBookings',
            'blockedBookings',
            'eligibleAmount',
            'eligibleCount',
            'blockedCount'
        ));
    }

    public function printClearancePdf(Request $request)
    {
        if (auth()->id() != 1 && (!auth()->user() || (!auth()->user()->can('clearance transport-handover') && !auth()->user()->can('view transport-handover')))) {
            return unauthorizedRedirect();
        }

        $setting = Setting::first();
        $excludedCustomerIds = self::getExcludedCustomerIds();

        $startDate = $request->filled('start_date') ? Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedDriverId = $request->driver_id;

        $query = TransportBooking::with(['salesInvoice', 'customer.city', 'customer.place', 'deliveryAgent'])
            ->where('status', 'Booked')
            ->where('payment_status', 'Cleared')
            ->whereNotNull('lr_no')
            ->where('lr_no', '!=', '')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_cleared_date', [$startDate, $endDate])
                  ->orWhereBetween('booking_date', [$startDate, $endDate])
                  ->orWhereBetween('created_at', [$startDate, $endDate]);
            });

        if (!empty($excludedCustomerIds)) {
            $query->whereNotIn('customer_id', $excludedCustomerIds);
        }

        if (!empty($selectedDriverId)) {
            if (is_numeric($selectedDriverId)) {
                $query->where(function ($q) use ($selectedDriverId) {
                    $q->where('delivery_agent_id', $selectedDriverId)
                      ->orWhere('delivery_agent_name', $selectedDriverId);
                });
            } else {
                $cleanDriver = preg_replace('/\s*\((Outside Driver|Staff Driver|Outside)\)$/i', '', $selectedDriverId);
                $cleanDriver = trim($cleanDriver);
                $query->where('delivery_agent_name', $cleanDriver);
            }
        }

        $bookings = $query->orderBy('payment_cleared_date', 'desc')->orderBy('id', 'desc')->get();

        $totalAmount = $bookings->sum('lr_price');
        $driverLabel = $selectedDriverId ?: 'All Drivers';
        $periodLabel = $startDate->format('d-m-Y') . ' to ' . $endDate->format('d-m-Y');
        $title = "Driver Freight Payout Clearance Sheet - " . $driverLabel;

        $pdf = Pdf::loadView('transport_bookings.clearance_pdf', [
            'bookings' => $bookings,
            'setting' => $setting,
            'title' => $title,
            'driverLabel' => $driverLabel,
            'periodLabel' => $periodLabel,
            'totalAmount' => $totalAmount,
            'totalBoxes' => $bookings->count()
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Driver_Clearance_Payout_' . date('Ymd_His') . '.pdf');
    }

    public function sendDispatchCardEmail(TransportBooking $booking, $targetEmail = null)
    {
        $booking->loadMissing(['customer', 'salesInvoice']);
        $recipient = $targetEmail ?: ($booking->customer ? ($booking->customer->email ?: $booking->customer->contact_email) : null);

        if (empty($recipient)) {
            return [
                'sent' => false,
                'reason' => 'No customer email address on file',
                'email' => null
            ];
        }

        try {
            $cardData = DispatchCardMail::prepareCardData($booking);
            $invNo = $cardData['invoice_no'];
            $subject = "See You Soon! Your Package is Out for Delivery [{$invNo}] - Casino";

            $htmlBody = view('emails.dispatch_card', $cardData)->render();

            $pdf = Pdf::loadView('transport_bookings.dispatch_card_pdf', $cardData)->setPaper('a4', 'portrait');
            $pdfContent = $pdf->output();
            $pdfFileName = "Casino_Dispatch_Notification_" . str_replace(['/', '\\', ' '], '_', $invNo) . ".pdf";

            $fromEmail = config('mail.from.address') ?: 'dispatch@erp.nachias.com';
            $fromName = config('mail.from.name') ?: 'Casino Dispatch - Nachias';

            $boundary = "----=_NextPart_" . md5(time() . uniqid());

            $headers = "From: {$fromName} <{$fromEmail}>\r\n";
            $headers .= "Reply-To: {$fromEmail}\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

            $body = "--{$boundary}\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= chunk_split(base64_encode($htmlBody)) . "\r\n";

            if (!empty($pdfContent)) {
                $body .= "--{$boundary}\r\n";
                $body .= "Content-Type: application/pdf; name=\"{$pdfFileName}\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"{$pdfFileName}\"\r\n\r\n";
                $body .= chunk_split(base64_encode($pdfContent)) . "\r\n";
            }
            $body .= "--{$boundary}--";

            $sent = @mail($recipient, $subject, $body, $headers);

            if ($sent) {
                $booking->customer_notified = 1;
                $booking->notified_at = Carbon::now();
                $booking->save();

                if (function_exists('addLog')) {
                    addLog(
                        'send_dispatch_email',
                        'Transport Handover',
                        'transport_bookings',
                        $booking->id,
                        null,
                        ['email' => $recipient, 'lr_no' => $booking->lr_no],
                        "Customer Dispatch Notification Email sent to {$recipient} for Invoice {$invNo} (LR: {$booking->lr_no})"
                    );
                }

                return [
                    'sent' => true,
                    'reason' => 'Email sent successfully',
                    'email' => $recipient
                ];
            } else {
                $error = error_get_last();
                return [
                    'sent' => false,
                    'reason' => 'Server mail error: ' . ($error['message'] ?? 'Could not deliver'),
                    'email' => $recipient
                ];
            }
        } catch (\Throwable $e) {
            Log::warning("Customer dispatch card email skipped/failed for booking {$booking->id}: " . $e->getMessage());
            
            $friendlyReason = $e->getMessage();

            return [
                'sent' => false,
                'reason' => $friendlyReason,
                'email' => $recipient
            ];
        }
    }

    public function dispatchCard($id)
    {
        $booking = TransportBooking::findOrFail($id);
        $cardData = DispatchCardMail::prepareCardData($booking);

        return view('transport_bookings.dispatch_card_view', $cardData);
    }

    public function dispatchCardPdf($id)
    {
        $booking = TransportBooking::findOrFail($id);
        $cardData = DispatchCardMail::prepareCardData($booking);
        $pdf = Pdf::loadView('transport_bookings.dispatch_card_pdf', $cardData)->setPaper('a4', 'portrait');
        $fileName = 'Casino_Dispatch_' . str_replace(['/', '\\', ' '], '_', $cardData['invoice_no']) . '.pdf';
        return $pdf->stream($fileName);
    }

    public function sendDispatchEmail(Request $request, $id)
    {
        $booking = TransportBooking::findOrFail($id);
        $targetEmail = trim($request->input('customer_email'));

        $result = $this->sendDispatchCardEmail($booking, $targetEmail);

        if ($result['sent']) {
            return back()->with('success', "Dispatch notification email sent successfully to {$result['email']}!");
        } else {
            return back()->with('error', "Could not send email: " . $result['reason']);
        }
    }

    public function syncExistingInvoices(Request $request)
    {
        try {
            $inserted = DB::affectingStatement("
                INSERT INTO `transport_bookings` (
                    `sales_invoice_id`,
                    `customer_id`,
                    `box_number`,
                    `box_index`,
                    `total_boxes`,
                    `quantity`,
                    `preferred_transport`,
                    `booking_office`,
                    `booking_rate`,
                    `lr_no`,
                    `booking_date`,
                    `status`,
                    `payment_status`,
                    `created_by`,
                    `created_at`,
                    `updated_at`
                )
                SELECT 
                    si.id AS sales_invoice_id,
                    si.customer_id,
                    '1/1' AS box_number,
                    1 AS box_index,
                    1 AS total_boxes,
                    COALESCE((SELECT SUM(sii.quantity) FROM `sales_invoice_items` sii WHERE sii.sales_invoice_id = si.id), 0) AS quantity,
                    COALESCE(si.transporter_name, c.transport_name, '-') AS preferred_transport,
                    COALESCE(c.booking_office, ct.city_name, pl.place_name, '') AS booking_office,
                    0.00 AS booking_rate,
                    si.lr_no,
                    CASE 
                        WHEN si.lr_no IS NOT NULL AND TRIM(si.lr_no) != '' THEN si.inv_date 
                        ELSE NULL 
                    END AS booking_date,
                    CASE 
                        WHEN si.lr_no IS NOT NULL AND TRIM(si.lr_no) != '' THEN 'Booked' 
                        ELSE 'Ready for Delivery' 
                    END AS status,
                    'Unapproved' AS payment_status,
                    1 AS created_by,
                    COALESCE(si.created_at, NOW()) AS created_at,
                    COALESCE(si.updated_at, NOW()) AS updated_at
                FROM `sales_invoices` si
                LEFT JOIN `customers` c ON c.id = si.customer_id
                LEFT JOIN `cities` ct ON ct.id = c.city_id
                LEFT JOIN `places` pl ON pl.id = c.place_id
                WHERE si.invoice_status != 'Cancelled'
                  AND (c.name IS NULL OR (c.name NOT LIKE '%CASH%' AND c.name NOT LIKE '%ARUMUGAM%' AND c.name NOT LIKE '%SRIVARSAN%'))
                  AND NOT EXISTS (
                      SELECT 1 FROM `transport_bookings` tb WHERE tb.sales_invoice_id = si.id
                  )
            ");

            $msg = "Successfully synced {$inserted} existing invoices into Transport Handover Register!";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'inserted' => $inserted
                ]);
            }

            return redirect()->route('transport_bookings.index')->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Transport bookings sync error: ' . $e->getMessage());
            return redirect()->route('transport_bookings.index')->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}
