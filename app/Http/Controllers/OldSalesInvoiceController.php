<?php

namespace App\Http\Controllers;

use App\Models\OldSalesInvoice;
use App\Models\OldSalesInvoiceItem;
use App\Models\Brand;
use App\Models\Style;
use App\Models\Customer;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use ZipArchive;

class OldSalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = OldSalesInvoice::with(['brand', 'customer'])
                ->orderBy('id', 'desc');

            if (!empty($request->customer_id)) {
                $query->where('customer_id', $request->customer_id);
            }
            if (!empty($request->brand_id)) {
                $query->where('brand_id', $request->brand_id);
            }
            if (!empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween('doc_date', [$request->from_date, $request->to_date]);
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('doc_no', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('gstin_reg_no', 'like', "%{$search}%")
                        ->orWhere('total_amount', 'like', "%{$search}%")
                        ->orWhere('irn_no', 'like', "%{$search}%");
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $invoices = $query->get();
            $data = [];
            $count = $start + 1;

            foreach ($invoices as $inv) {
                $action = '<div class="button-box d-flex gap-1 align-items-center justify-content-center">';
                $action .= '<a href="' . url('old_sales_invoices/view/' . $inv->id) . '" class="btn btn-view" title="View Details"><i class="icon-base ri ri-eye-line"></i></a>';
                $action .= '<a href="' . url('old_sales_invoices/pdf/' . $inv->id) . '" class="btn btn-pdf" target="_blank" title="Download PDF"><i class="icon-base ri ri-file-pdf-line"></i></a>';
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'doc_no' => $inv->doc_no,
                    'doc_date' => $inv->doc_date ? $inv->doc_date->format('d-m-Y') : '-',
                    'customer_name' => $inv->customer_name ?: ($inv->customer?->name ?? '-'),
                    'gstin_reg_no' => $inv->gstin_reg_no ?: '-',
                    'brand_name' => $inv->brand?->brand_name ?: '-',
                    'total_qty' => number_format($inv->total_qty, 0),
                    'total_amount' => '₹' . number_format($inv->total_amount, 2),
                    'action' => $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        $brands = Brand::active()->orderBy('id','desc')->get();
        $customers = Customer::orderBy('id','desc')->get();
        return view('old_sales_invoices.index', compact('brands', 'customers'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx,csv|max:10240', 
        ], [
            'excel_file.required' => 'Please select an Excel or CSV file to import.',
            'excel_file.mimes' => 'Only .xlsx, .xls, and .csv files are supported.',
            'excel_file.max' => 'The file size exceeds the maximum allowed limit of 10 MB. Please upload a smaller file.',
        ]);

        $file = $request->file('excel_file');
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'The uploaded file contains no data.');
            }

            // 1. Validate Column Headers presence
            $firstRow = reset($rows);
            $requiredHeaderCols = [
                'B' => 'Doc No',
                'C' => 'Doc Date',
                'D' => 'Customer Name',
                'N' => 'Description / Item Name',
                'P' => 'Size',
                'S' => 'Price / Rate',
                'T' => 'Quantity',
            ];

            // If the first row looks like a header row
            $firstColB = trim((string)($firstRow['B'] ?? ''));
            $firstColD = trim((string)($firstRow['D'] ?? ''));
            $isHeader = !empty($firstColB) && !is_numeric($firstColB) && (
                stripos($firstColB, 'doc') !== false || 
                stripos($firstColB, 'inv') !== false ||
                stripos($firstColD, 'cust') !== false ||
                stripos($firstColD, 'party') !== false ||
                stripos($firstColD, 'name') !== false
            );

            if ($isHeader) {
                $missingHeaders = [];
                foreach ($requiredHeaderCols as $colKey => $colName) {
                    $headerVal = trim((string)($firstRow[$colKey] ?? ''));
                    if (empty($headerVal)) {
                        $missingHeaders[] = "<strong>Column {$colKey}</strong> (Expected: <em>{$colName}</em>)";
                    }
                }

                if (!empty($missingHeaders)) {
                    return back()->with('error', 'Import failed! The following required column(s) are missing or misplaced in the uploaded Excel header:<br><br>• ' . implode('<br>• ', $missingHeaders) . '<br><br>Please verify your Excel file columns before uploading.');
                }
            }

            $allBrands = Brand::all();
            $allStyles = Style::all();

            $groupedInvoices = [];
            $headerRowFound = false;
            $rowErrors = [];

            foreach ($rows as $rowIndex => $row) {
                // Determine if this is header
                $colA = trim((string)($row['A'] ?? ''));
                $colB = trim((string)($row['B'] ?? ''));
                $colC = trim((string)($row['C'] ?? ''));

                if (strcasecmp($colA, 'S.No') === 0 || strcasecmp($colB, 'Doc No') === 0 || strcasecmp($colC, 'Doc Date') === 0) {
                    $headerRowFound = true;
                    continue;
                }

                if (!$headerRowFound && $rowIndex === 1) {
                    continue; // Skip first row if it was header
                }

                $docNo = trim((string)($row['B'] ?? ''));
                $description = trim((string)($row['N'] ?? ''));
                $qtyRaw = trim((string)($row['T'] ?? ''));
                $priceRaw = trim((string)($row['S'] ?? ''));

                // Skip completely empty rows
                if (empty($docNo) && empty($description) && empty($qtyRaw)) {
                    continue;
                }

                if (empty($docNo)) {
                    $rowErrors[] = "Row #{$rowIndex}: <strong>Doc No</strong> is missing.";
                    continue;
                }

                if (empty($description)) {
                    $rowErrors[] = "Row #{$rowIndex} (Doc No: <strong>{$docNo}</strong>): <strong>Description / Art No</strong> is missing.";
                    continue;
                }

                if (!isset($groupedInvoices[$docNo])) {
                    $docDateRaw = $row['C'] ?? null;
                    $docDate = null;
                    if ($docDateRaw) {
                        if (is_numeric($docDateRaw)) {
                            $docDate = Carbon::instance(ExcelDate::excelToDateTimeObject($docDateRaw))->format('Y-m-d');
                        } else {
                            try {
                                $docDate = Carbon::parse($docDateRaw)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $docDate = null;
                            }
                        }
                    }

                    $groupedInvoices[$docNo] = [
                        'doc_no' => $docNo,
                        'doc_date' => $docDate,
                        'customer_name' => trim((string)($row['D'] ?? '')),
                        'gstin_reg_no' => trim((string)($row['E'] ?? '')),
                        'vehicle_no' => trim((string)($row['F'] ?? '')),
                        'pincode' => trim((string)($row['G'] ?? '')),
                        'bill_to' => trim((string)($row['H'] ?? '')),
                        'ship_to' => trim((string)($row['I'] ?? '')),
                        'irn_no' => trim((string)($row['J'] ?? '')),
                        'ack_no' => trim((string)($row['K'] ?? '')),
                        'ack_date' => trim((string)($row['L'] ?? '')),
                        'total_amount' => floatval(str_replace(',', '', (string)($row['M'] ?? 0))),
                        'courier_charges' => 0,
                        'items' => [],
                    ];
                }

                $freightAmt = floatval(str_replace(',', '', (string)($row['AG'] ?? 0)));
                if ($freightAmt > ($groupedInvoices[$docNo]['courier_charges'] ?? 0)) {
                    $groupedInvoices[$docNo]['courier_charges'] = $freightAmt;
                }

                $sNo = intval($row['A'] ?? (count($groupedInvoices[$docNo]['items']) + 1));
                $description = trim((string)($row['N'] ?? ''));
                $class1BrandName = trim((string)($row['O'] ?? ''));
                $size = trim((string)($row['P'] ?? ''));
                $numericSize = preg_replace('/[^0-9]/', '', $size);
                if (!empty($numericSize)) {
                    $size = $numericSize;
                }
                $class2Sleeve = trim((string)($row['Q'] ?? ''));
                $hsnSac = trim((string)($row['R'] ?? ''));
                if (empty($hsnSac)) {
                    $hsnSac = '62053000';
                }
                $price = floatval(str_replace(',', '', (string)($row['S'] ?? 0)));
                $mrp = round($price * 1.5, 2);
                $qty = floatval(str_replace(',', '', (string)($row['T'] ?? 0)));
                $grossAmt = floatval(str_replace(',', '', (string)($row['U'] ?? ($price * $qty))));
                $discAmt = floatval(str_replace(',', '', (string)($row['V'] ?? 0)));
                $taxableAmt = max(0, $grossAmt - $discAmt);

                $resolved = $this->resolveBrandAndStyle($description, $class1BrandName, $allBrands, $allStyles);

                $groupedInvoices[$docNo]['items'][] = [
                    'description' => $description,
                    'class1_raw' => $class1BrandName,
                    'brand_id' => $resolved['brand_id'],
                    'style_id' => $resolved['style_id'],
                    'size' => $size,
                    'class2_sleeve' => $class2Sleeve,
                    'hsn_sac' => $hsnSac,
                    'mrp' => $mrp,
                    'price' => $price,
                    'quantity' => $qty,
                    'gross_amount' => $grossAmt,
                    'discount_amount' => $discAmt,
                    'taxable_amount' => $taxableAmt,
                ];
            }

            if (!empty($rowErrors)) {
                $displayedErrors = array_slice($rowErrors, 0, 10);
                $remainingCount = count($rowErrors) - count($displayedErrors);
                $errorMsg = "Import failed! Found " . count($rowErrors) . " data validation error(s) in Excel rows:<br><br>• " . implode('<br>• ', $displayedErrors);
                if ($remainingCount > 0) {
                    $errorMsg .= "<br>• <em>...and {$remainingCount} more row errors.</em>";
                }
                return back()->with('error', $errorMsg);
            }

            if (empty($groupedInvoices)) {
                return back()->with('error', 'No valid invoice rows found in the uploaded file.');
            }

            $missingCustomerNames = [];
            $resolvedCustomers = [];
            $missingBrandNames = [];

            foreach ($groupedInvoices as $docNo => $invData) {
                // 1. Customer Check
                $customerId = null;
                $gstin = trim($invData['gstin_reg_no'] ?? '');
                $custName = trim($invData['customer_name'] ?? '');

                if (!empty($gstin)) {
                    $matchedCust = Customer::where('gst_no', $gstin)->first();
                    if ($matchedCust) {
                        $customerId = $matchedCust->id;
                    }
                }

                if (!$customerId && !empty($custName)) {
                    $matchedCust = Customer::where('name', $custName)->first();
                    
                    if (!$matchedCust && str_contains($custName, '-')) {
                        $namePart = trim(explode('-', $custName)[0]);
                        if (!empty($namePart)) {
                            $matchedCust = Customer::where('name', $namePart)->first();
                        }
                    }

                    if (!$matchedCust) {
                        $matchedCust = Customer::whereRaw('? LIKE CONCAT(name, "%")', [$custName])
                            ->orWhere('name', 'like', $custName . '%')
                            ->first();
                    }

                    if ($matchedCust) {
                        $customerId = $matchedCust->id;
                    }
                }

                if (!$customerId) {
                    $cName = $custName ?: ($gstin ?: "Doc: {$docNo}");
                    if (!in_array($cName, $missingCustomerNames)) {
                        $missingCustomerNames[] = $cName;
                    }
                } else {
                    $resolvedCustomers[$docNo] = $customerId;
                }

                // 2. Brand Check
                foreach ($invData['items'] as $item) {
                    if (empty($item['brand_id'])) {
                        $bName = !empty($item['class1_raw']) ? $item['class1_raw'] : trim(explode(' ', $item['description'] ?? '')[0]);
                        if (!empty($bName) && !in_array($bName, $missingBrandNames)) {
                            $missingBrandNames[] = $bName;
                        }
                    }
                }
            }

            $errors = [];

            if (!empty($missingCustomerNames)) {
                $custNamesStr = implode(', ', array_slice($missingCustomerNames, 0, 5));
                if (count($missingCustomerNames) > 5) {
                    $custNamesStr .= ' (and ' . (count($missingCustomerNames) - 5) . ' more)';
                }
                $errors[] = "Customer not exists in master table: <strong>{$custNamesStr}</strong>";
            }

            if (!empty($missingBrandNames)) {
                $brandNamesStr = implode(', ', array_slice($missingBrandNames, 0, 5));
                if (count($missingBrandNames) > 5) {
                    $brandNamesStr .= ' (and ' . (count($missingBrandNames) - 5) . ' more)';
                }
                $errors[] = "Brand not exists in master table: <strong>{$brandNamesStr}</strong>";
            }

            if (!empty($errors)) {
                return back()->with('error', 'Import failed! ' . implode('<br>', $errors));
            }

            DB::beginTransaction();

            $importedCount = 0;
            $itemsCount = 0;

            foreach ($groupedInvoices as $docNo => $invData) {
                $customerId = $resolvedCustomers[$docNo] ?? null;

                $totalQty = 0;
                $subTotal = 0;
                $discountTotal = 0;
                $taxableTotal = 0;
                $primaryBrandId = null;

                foreach ($invData['items'] as $item) {
                    $totalQty += $item['quantity'];
                    $subTotal += $item['gross_amount'];
                    $discountTotal += $item['discount_amount'];
                    $taxableTotal += $item['taxable_amount'];
                    if (!$primaryBrandId && !empty($item['brand_id'])) {
                        $primaryBrandId = $item['brand_id'];
                    }
                }

                // Check state from GSTIN
                $gstin = $invData['gstin_reg_no'] ?? '';
                $isOtherState = false;
                if (!empty($gstin) && strlen($gstin) >= 2) {
                    $stateCode = substr($gstin, 0, 2);
                    $isOtherState = ($stateCode !== '33');
                }

                $cgstAmt = 0;
                $sgstAmt = 0;
                $igstAmt = 0;

                if ($isOtherState) {
                    $igstAmt = round($taxableTotal * 0.05, 2);
                } else {
                    $cgstAmt = round($taxableTotal * 0.025, 2);
                    $sgstAmt = round($taxableTotal * 0.025, 2);
                }

                $courierCharges = $invData['courier_charges'] ?? 0;
                $calculatedGrandTotal = $taxableTotal + $cgstAmt + $sgstAmt + $igstAmt + $courierCharges;
                $roundOff = 0;
                if ($invData['total_amount'] > 0) {
                    $roundOff = round($invData['total_amount'] - $calculatedGrandTotal, 2);
                    $finalGrandTotal = $invData['total_amount'];
                } else {
                    $finalGrandTotal = round($calculatedGrandTotal);
                    $roundOff = round($finalGrandTotal - $calculatedGrandTotal, 2);
                }

                $oldInvoice = OldSalesInvoice::updateOrCreate(
                    ['doc_no' => $docNo],
                    [
                        'doc_date' => $invData['doc_date'],
                        'customer_id' => $customerId,
                        'customer_name' => $invData['customer_name'],
                        'gstin_reg_no' => $invData['gstin_reg_no'],
                        'vehicle_no' => $invData['vehicle_no'],
                        'pincode' => $invData['pincode'],
                        'bill_to' => $invData['bill_to'],
                        'ship_to' => $invData['ship_to'],
                        'brand_id' => $primaryBrandId,
                        'irn_no' => $invData['irn_no'],
                        'ack_no' => $invData['ack_no'],
                        'ack_date' => $invData['ack_date'],
                        'total_qty' => $totalQty,
                        'sub_total' => $subTotal,
                        'discount_amount' => $discountTotal,
                        'taxable_amount' => $taxableTotal,
                        'cgst_amount' => $cgstAmt,
                        'sgst_amount' => $sgstAmt,
                        'igst_amount' => $igstAmt,
                        'courier_charges' => $courierCharges,
                        'round_off' => $roundOff,
                        'total_amount' => $finalGrandTotal,
                        'created_by' => auth()->id(),
                    ]
                );

                // Re-create items
                OldSalesInvoiceItem::where('old_sales_invoice_id', $oldInvoice->id)->delete();

                foreach ($invData['items'] as $item) {
                    OldSalesInvoiceItem::create([
                        'old_sales_invoice_id' => $oldInvoice->id,
                        'doc_no' => $docNo,
                        'description' => $item['description'],
                        'brand_id' => $item['brand_id'],
                        'style_id' => $item['style_id'],
                        'size' => $item['size'],
                        'class2_sleeve' => $item['class2_sleeve'],
                        'hsn_sac' => $item['hsn_sac'],
                        'mrp' => $item['mrp'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'gross_amount' => $item['gross_amount'],
                    ]);
                    $itemsCount++;
                }

                $importedCount++;
            }

            DB::commit();

            return back()->with('success', "Successfully imported {$importedCount} invoices with {$itemsCount} item lines.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    protected function resolveBrandAndStyle($description, $class1BrandName, $brands, $styles)
    {
        $desc = strtoupper(trim($description));
        $class1 = strtoupper(trim($class1BrandName));

        $brandId = null;
        $styleId = null;

        // Extract series/number from description (e.g., CB1122-4, CF20797-2, CDC6001, etc.)
        $artNumber = 0;
        $prefix = '';

        if (preg_match('/^([A-Z]+)\s*(\d+)/', $desc, $matches)) {
            $prefix = $matches[1];
            $artNumber = intval($matches[2]);
        }

        $styleName = 'PLAIN'; // Default
        $targetBrandCode = '';

        if ($prefix === 'CFC' || (str_starts_with($desc, 'CFC') && $artNumber >= 7000 && $artNumber < 8000)) {
            $targetBrandCode = 'CF';
            $styleName = 'PLAIN';
        } elseif ($prefix === 'CBC' || (str_starts_with($desc, 'CBC') && $artNumber >= 8000 && $artNumber < 9000)) {
            $targetBrandCode = 'CBC';
            $styleName = 'PLAIN';
        } elseif ($prefix === 'CDC' || (str_starts_with($desc, 'CDC') && $artNumber >= 6000 && $artNumber < 7000)) {
            $targetBrandCode = 'CDC';
            $styleName = 'PLAIN';
        } elseif ($prefix === 'CF' || str_starts_with($desc, 'CF')) {
            $targetBrandCode = 'CFC';
            if ($artNumber >= 30000 && $artNumber < 40000) {
                $styleName = 'CHECKED';
            } elseif ($artNumber >= 20000 && $artNumber < 30000) {
                $styleName = 'PRINT';
            } else {
                $styleName = 'PLAIN';
            }
        } elseif ($prefix === 'CB' || str_starts_with($desc, 'CB')) {
            $targetBrandCode = 'CB';
            if ($artNumber >= 3000 && $artNumber < 4000) {
                $styleName = 'CHECKED';
            } elseif ($artNumber >= 2000 && $artNumber < 3000) {
                $styleName = 'PRINT';
            } else {
                $styleName = 'PLAIN';
            }
        } elseif ($prefix === 'CDW' || str_starts_with($desc, 'CDW')) {
            $targetBrandCode = 'CDW';
            if ($artNumber >= 60000 && $artNumber < 70000) {
                $styleName = 'CHECKED';
            } elseif ($artNumber >= 50000 && $artNumber < 60000) {
                $styleName = 'PRINT';
            } else {
                $styleName = 'PLAIN';
            }
        } elseif ($prefix === 'CW' || str_contains($desc, 'CASINO WHITE')) {
            $targetBrandCode = 'CW';
            $styleName = 'PLAIN';
        } elseif ($prefix === 'CDS' || str_contains($desc, 'CASINO DHOTI SET')) {
            $targetBrandCode = 'CDS';
            $styleName = 'PLAIN';
        }

        // Find Brand ID
        if (!empty($targetBrandCode)) {
            $matchedBrand = $brands->firstWhere('code', $targetBrandCode);
            if ($matchedBrand) {
                $brandId = $matchedBrand->id;
            }
        }

        if (!$brandId && !empty($class1)) {
            $matchedBrand = $brands->first(function ($b) use ($class1) {
                return strcasecmp($b->brand_name, $class1) === 0 || strcasecmp($b->code, $class1) === 0;
            });
            if ($matchedBrand) {
                $brandId = $matchedBrand->id;
            }
        }

        // Find Style ID
        $matchedStyle = $styles->first(function ($s) use ($styleName) {
            return strcasecmp($s->style_name, $styleName) === 0 || strcasecmp($s->code, $styleName) === 0;
        });

        if ($matchedStyle) {
            $styleId = $matchedStyle->id;
        }

        return [
            'brand_id' => $brandId,
            'style_id' => $styleId,
            'style_name' => $styleName
        ];
    }

    public function view($id)
    {
        $invoice = OldSalesInvoice::with(['brand', 'customer', 'items.brand', 'items.style'])->findOrFail($id);
        return view('old_sales_invoices.view_details', compact('invoice'));
    }

    public function downloadPdf($id, Request $request)
    {
        $invoice = OldSalesInvoice::with(['brand', 'customer.state', 'customer.city', 'customer.zone', 'items.brand', 'items.style'])->findOrFail($id);
        $setting = Setting::with(['state', 'city'])->first();

        $showFieldsParam = $request->input('show_fields');
        if (is_array($showFieldsParam)) {
            $showFields = $showFieldsParam;
        } elseif (is_string($showFieldsParam) && strlen(trim($showFieldsParam)) > 0) {
            $showFields = array_map('trim', explode(',', $showFieldsParam));
        } else {
            $showFields = ['amount', 'subtotal', 'discount', 'grandtotal', 'tax', 'mrp', 'price'];
        }

        $taxSummary = [];
        $totalGross = $invoice->sub_total > 0 ? (float)$invoice->sub_total : (float)$invoice->items->sum('gross_amount');
        $discountRatio = $totalGross > 0 ? ((float)$invoice->discount_amount / $totalGross) : 0;

        foreach ($invoice->items as $item) {
            $hsn = $item->hsn_sac ?: '62053000';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $invoice->cgst_amount > 0 ? 2.5 : 0,
                    'cgst_amount' => 0,
                    'sgst_rate' => $invoice->sgst_amount > 0 ? 2.5 : 0,
                    'sgst_amount' => 0,
                    'igst_rate' => $invoice->igst_amount > 0 ? 5.0 : 0,
                    'igst_amount' => 0,
                ];
            }
            $itemTaxable = $item->gross_amount * (1 - $discountRatio);
            $taxSummary[$hsn]['taxable_value'] += $itemTaxable;
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        $totalInWords = numberToWords($invoice->total_amount);
        $taxAmount = (float)$invoice->cgst_amount + (float)$invoice->sgst_amount + (float)$invoice->igst_amount;
        $totalTaxInWords = numberToWords($taxAmount);

        $pdf = Pdf::loadView('old_sales_invoices.pdf', compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords', 'showFields'));
        $pdf->setPaper('A4', 'portrait');

        $safeDocNo = str_replace(['/', '\\'], '_', $invoice->doc_no);
        return $pdf->stream('Sales_Invoice_' . $safeDocNo . '.pdf');
    }

    public function downloadBulkZip(Request $request)
    {
        $invoiceIds = $request->input('invoice_ids', []);
        $query = OldSalesInvoice::with(['brand', 'customer.state', 'customer.city', 'customer.zone', 'items.brand', 'items.style']);

        if (!empty($invoiceIds)) {
            $query->whereIn('id', $invoiceIds);
        }

        $invoices = $query->get();
        if ($invoices->isEmpty()) {
            return back()->with('error', 'No invoices found to export.');
        }

        $setting = Setting::with(['state', 'city'])->first();
        $zipFileName = 'Old_Sales_Invoices_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Could not create zip file.');
        }

        foreach ($invoices as $invoice) {
            $taxSummary = [];
            foreach ($invoice->items as $item) {
                $hsn = $item->hsn_sac ?: '62052000';
                if (!isset($taxSummary[$hsn])) {
                    $taxSummary[$hsn] = [
                        'hsn' => $hsn,
                        'taxable_value' => 0,
                        'cgst_rate' => $invoice->cgst_amount > 0 ? 2.5 : 0,
                        'cgst_amount' => 0,
                        'sgst_rate' => $invoice->sgst_amount > 0 ? 2.5 : 0,
                        'sgst_amount' => 0,
                        'igst_rate' => $invoice->igst_amount > 0 ? 5.0 : 0,
                        'igst_amount' => 0,
                    ];
                }
                $taxSummary[$hsn]['taxable_value'] += $item->gross_amount;
            }

            foreach ($taxSummary as &$summary) {
                $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
                $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
                $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
            }

            $totalInWords = numberToWords($invoice->total_amount);
            $taxAmount = $invoice->cgst_amount + $invoice->sgst_amount + $invoice->igst_amount;
            $totalTaxInWords = numberToWords($taxAmount);

            $pdf = Pdf::loadView('old_sales_invoices.pdf', compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords'));
            $pdf->setPaper('A4', 'portrait');

            $safeDocNo = str_replace(['/', '\\'], '_', $invoice->doc_no);
            $zip->addFromString('Invoice_' . $safeDocNo . '.pdf', $pdf->output());
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function destroy($id)
    {
        $invoice = OldSalesInvoice::findOrFail($id);
        $invoice->delete();
        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully.']);
    }
}
