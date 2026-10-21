<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\TransportBooking;
use App\Models\User;
use App\Models\Setting;
use App\Services\TransportBookingService;
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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = TransportBooking::with(['salesInvoice', 'customer.city', 'customer.place', 'deliveryAgent'])->orderBy('sales_invoice_id', 'desc')->orderBy('id', 'asc');

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

            if ($request->filled('driver_id')) {
                $query->where('delivery_agent_id', $request->driver_id);
            }

            if ($request->filled('transport_name')) {
                $query->where('preferred_transport', 'like', "%{$request->transport_name}%");
            }

            if ($request->filled('date')) {
                $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
                $query->whereDate('created_at', $date);
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

            $data = [];
            foreach ($bookings as $idx => $b) {
                $custName = $b->customer ? $b->customer->name : 'N/A';
                $custCode = $b->customer ? $b->customer->code : '';
                $place = (!empty($b->booking_office) && $b->booking_office !== '-')
                    ? $b->booking_office
                    : ($b->customer && $b->customer->city ? $b->customer->city->city_name : ($b->customer && $b->customer->place ? $b->customer->place->place_name : ''));

                $placeDisplay = $place ? ($custCode ? "{$place} ({$custCode})" : $place) : ($custCode ? "({$custCode})" : '');
                $customerHtml = "<div><span class='fw-bold text-dark d-block'>{$custName}</span><small class='text-muted'>{$placeDisplay}</small></div>";
                
                $invNo = $b->salesInvoice ? $b->salesInvoice->inv_no : '-';
                $invUrl = url('sales_invoices/show/' . $b->sales_invoice_id);
                $invHtml = "<a href='{$invUrl}' class='fw-semibold text-primary' target='_blank'>{$invNo}</a>";

                $invDate = ($b->salesInvoice && $b->salesInvoice->inv_date)
                    ? Carbon::parse($b->salesInvoice->inv_date)->format('d-m-Y')
                    : '-';
                $invDateHtml = "<span class='text-nowrap text-secondary fw-medium'>{$invDate}</span>";

                $boxHtml = "<span class='badge bg-label-dark fw-bold'>Box {$b->box_number}</span>";

                $driverName = $b->deliveryAgent ? $b->deliveryAgent->name : ($b->delivery_agent_name ?: 'Unassigned');
                $driverHtml = "<span class='badge bg-label-primary'>{$driverName}</span>";

                $statusHtml = '';
                if ($b->status === 'Booked') {
                    $statusHtml = "<span class='badge bg-label-success'><i class='ri ri-checkbox-circle-line me-1'></i> Booked</span>";
                } elseif ($b->status === 'Picked') {
                    $statusHtml = "<span class='badge bg-label-warning'><i class='ri ri-truck-line me-1'></i> Picked</span>";
                } else {
                    $statusHtml = "<span class='badge bg-label-secondary'>Ready for Delivery</span>";
                }

                $lrHtml = '';
                if (!empty($b->lr_no)) {
                    $lrHtml = "<span class='badge bg-label-success fw-bold font-monospace fs-7'>{$b->lr_no}</span>";
                } else {
                    $lrHtml = "<button type='button' class='btn btn-xs btn-outline-danger btn-open-lr-modal' data-id='{$b->id}' data-invoice='{$invNo}' data-box='{$b->box_number}' data-customer='{$custName}' data-transport='{$b->preferred_transport}' data-rate='{$b->booking_rate}' data-lr='' data-driver='{$b->delivery_agent_id}'>+ Enter LR</button>";
                }

                $editBtn = "<button type='button' class='btn btn-sm btn-icon btn-text-primary rounded-pill btn-open-lr-modal' data-id='{$b->id}' data-invoice='{$invNo}' data-box='{$b->box_number}' data-customer='{$custName}' data-transport='{$b->preferred_transport}' data-rate='{$b->booking_rate}' data-lr='{$b->lr_no}' data-driver='{$b->delivery_agent_id}' title='Update LR Details'><i class='ri ri-pencil-line'></i></button>";
                $stickerBtn = "<a href='" . url('sales_invoices/transport-sticker/' . $b->sales_invoice_id) . "' target='_blank' class='btn btn-sm btn-icon btn-text-info rounded-pill ms-1' title='Print Sticker'><i class='ri ri-printer-line'></i></a>";

                $actionHtml = "<div class='d-flex align-items-center'>{$editBtn}{$stickerBtn}</div>";

                $data[] = [
                    'DT_RowIndex' => $start + $idx + 1,
                    'inv_no' => $invHtml,
                    'inv_date' => $invDateHtml,
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

        $customers = Customer::where('status', 'Active')->orderBy('name')->get(['id', 'name', 'code']);
        $drivers = User::whereHas('role', function ($q) {
            $q->where('name', 'DRIVER');
        })->orWhere('role_id', 11)->orderBy('name')->get(['id', 'name']);

        $totalBoxesCount = TransportBooking::count();
        $bookedBoxesCount = TransportBooking::where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->count();
        $pendingLrCount = TransportBooking::where(function ($q) {
            $q->whereNull('lr_no')->orWhere('lr_no', '');
        })->count();

        return view('transport_bookings.index', compact(
            'customers',
            'drivers',
            'totalBoxesCount',
            'bookedBoxesCount',
            'pendingLrCount'
        ));
    }

    public function recordLr(Request $request, $id)
    {
        $validated = $request->validate([
            'lr_no' => 'required|string',
            'lr_price' => 'nullable|numeric|min:0',
            'preferred_transport' => 'nullable|string',
            'delivery_agent_id' => 'nullable|integer',
            'booking_date' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            $driver = !empty($validated['delivery_agent_id']) ? User::find($validated['delivery_agent_id']) : null;
            $validated['delivery_agent_name'] = $driver ? $driver->name : null;
            $validated['status'] = 'Booked';

            $booking = $this->bookingService->recordLrBooking($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'LR ' . $booking->lr_no . ' saved successfully! Status updated to Booked & customer alert scheduled.',
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
        if ($request->filled('driver_id')) {
            $driver = User::find($request->driver_id);
            $booking->delivery_agent_id = $driver ? $driver->id : null;
            $booking->delivery_agent_name = $driver ? $driver->name : null;
        }
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => "Box {$booking->box_number} marked as Picked."
        ]);
    }

    public function printSheet(Request $request)
    {
        $setting = Setting::first();

        $query = TransportBooking::with(['salesInvoice', 'customer.city', 'customer.place', 'deliveryAgent']);

        if ($request->filled('invoice_id')) {
            $query->where('sales_invoice_id', $request->invoice_id);
            $title = "Transport Handover Sheet - Invoice #" . $request->invoice_id;
        } elseif ($request->filled('date')) {
            $date = Carbon::createFromFormat('d-m-Y', $request->date)->format('Y-m-d');
            $query->whereDate('created_at', $date);
            $title = "Daily Transport Handover Sheet - " . $request->date;
        } else {
            $query->whereDate('created_at', Carbon::today());
            $title = "Today's Transport Handover Sheet - " . date('d-m-Y');
        }

        $bookings = $query->orderBy('sales_invoice_id', 'desc')->get();

        $pdf = Pdf::loadView('transport_bookings.sheet_pdf', [
            'bookings' => $bookings,
            'setting' => $setting,
            'title' => $title,
            'date' => $request->date ?: date('d-m-Y')
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Handover_Sheet_' . date('Ymd_His') . '.pdf');
    }

    public function paymentClearance(Request $request)
    {
        $drivers = User::whereHas('role', function ($q) {
            $q->where('name', 'DRIVER');
        })->orWhere('role_id', 11)->orderBy('name')->get();

        $startDate = $request->filled('start_date') ? Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
        $endDate = $request->filled('end_date') ? Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $selectedDriverId = $request->driver_id;

        $baseQuery = TransportBooking::with(['salesInvoice', 'customer', 'deliveryAgent'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($selectedDriverId) {
            $baseQuery->where('delivery_agent_id', $selectedDriverId);
        }

        if ($request->isMethod('post') && $request->action === 'clear_payment') {
            $eligibleIds = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->where('payment_status', '!=', 'Cleared')->pluck('id');

            TransportBooking::whereIn('id', $eligibleIds)->update([
                'payment_status' => 'Cleared',
                'payment_cleared_date' => Carbon::now(),
                'updated_by' => auth()->id() ?? 1,
            ]);

            return redirect()->route('transport_bookings.payment_clearance', $request->only(['driver_id', 'start_date', 'end_date']))->with('success', 'Weekly payout of ' . count($eligibleIds) . ' verified boxes successfully cleared!');
        }

        $eligibleBookings = (clone $baseQuery)->where('status', 'Booked')->whereNotNull('lr_no')->where('lr_no', '!=', '')->get();

        $blockedBookings = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('lr_no')->orWhere('lr_no', '');
            })
            ->get();

        $eligibleAmount = $eligibleBookings->sum('lr_price');

        return view('transport_bookings.payment_clearance', compact(
            'drivers',
            'selectedDriverId',
            'startDate',
            'endDate',
            'eligibleBookings',
            'blockedBookings',
            'eligibleAmount'
        ));
    }
}
