<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemPricesExport;
use App\Imports\ItemPricesImport;

class ItemPriceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view item-prices')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $prices = DB::table('item_prices')
                ->leftJoin(DB::raw('(SELECT finished_item_code, MAX(item_id) as item_id FROM stock_entry_items WHERE deleted_at IS NULL GROUP BY finished_item_code) as sei'), 'item_prices.finished_item_code', '=', 'sei.finished_item_code')
                ->leftJoin('items', 'sei.item_id', '=', 'items.id')
                ->leftJoin(DB::raw('(SELECT item_code, MAX(item_name) as item_name FROM barcode_masters GROUP BY item_code) as bm'), 'item_prices.finished_item_code', '=', 'bm.item_code')
                ->whereNull('item_prices.deleted_at')
                ->select(
                    DB::raw('MAX(item_prices.id) as id'),
                    'item_prices.finished_item_code',
                    'item_prices.art_no',
                    DB::raw('MIN(item_prices.selling_price) as min_selling_price'),
                    DB::raw('MAX(item_prices.selling_price) as max_selling_price'),
                    DB::raw('MIN(item_prices.unit_price) as min_unit_price'),
                    DB::raw('MAX(item_prices.unit_price) as max_unit_price'),
                    DB::raw('MAX(item_prices.effective_from) as effective_from'),
                    DB::raw('MAX(item_prices.status) as status'),
                    DB::raw('COALESCE(items.name, bm.item_name, "-") as item_display_name')
                )
                ->groupBy('item_prices.finished_item_code', 'item_prices.art_no', 'items.name', 'bm.item_name')
                ->orderBy('id', 'desc')
                ->get();

            $data = [];
            $i = 1;

            foreach ($prices as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit item-prices')) {
                    $action .= '<a href="' . url('item_prices/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete item-prices')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('item_prices/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $itemName = $row->finished_item_code;

                $selling_price = ($row->min_selling_price == $row->max_selling_price) 
                            ? number_format($row->min_selling_price, 2) 
                            : number_format($row->min_selling_price, 2) . ' - ' . number_format($row->max_selling_price, 2);

                $unit_price = ($row->min_unit_price == $row->max_unit_price) 
                            ? number_format($row->min_unit_price, 2) 
                            : number_format($row->min_unit_price, 2) . ' - ' . number_format($row->max_unit_price, 2);

                $data[] = [
                    'DT_RowIndex'    => $i++,
                    'item_name'      => $itemName,
                    'art_no'         => $row->art_no ?? '-',
                    'selling_price'  => $selling_price,
                    'unit_price'     => $unit_price,
                    'effective_from' => $row->effective_from ? Carbon::parse($row->effective_from)->format('d-m-Y') : '-',
                    'status'         => $status,
                    'action'         => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('item_prices.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit item-prices')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create item-prices')) {
                return unauthorizedRedirect();
            }
        }

        $price = $id ? ItemPrice::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            if ($request->has('row_update')) {
                if (auth()->id() != 1 && !auth()->user()->can('edit item-prices')) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
                }

                $request->validate([
                    'finished_item_code' => 'required|string',
                    'art_no'             => 'nullable|string',
                    'size'               => 'required|string',
                    'selling_price'      => 'required|numeric|min:0',
                    'unit_price'         => 'required|numeric|min:0',
                    'effective_from'     => 'required|date_format:d-m-Y',
                    'status'             => 'required|in:Active,Inactive',
                ]);

                $effectiveFrom = Carbon::createFromFormat('d-m-Y', $request->effective_from)->format('Y-m-d');
                $finishedItemCode = $request->finished_item_code;
                $artNo = $request->art_no;
                $size = $request->size;
                $sellingPrice = $request->selling_price;
                $unitPrice = $request->unit_price;
                $status = $request->status;

                $priceRow = ItemPrice::where('finished_item_code', $finishedItemCode)
                    ->where('art_no', $artNo)
                    ->where('size', $size)
                    ->first();

                $data = [
                    'finished_item_code' => $finishedItemCode,
                    'art_no'             => $artNo,
                    'size'               => $size,
                    'selling_price'      => $sellingPrice,
                    'unit_price'         => $unitPrice,
                    'effective_from'     => $effectiveFrom,
                    'status'             => $status,
                ];

                if ($priceRow) {
                    $data['updated_by'] = auth()->id() ?? 1;
                    $priceRow->update($data);
                    addLog('update', 'Item Price Row', 'item_prices', $priceRow->id, null, $data);
                } else {
                    $data['created_by'] = auth()->id() ?? 1;
                    $priceRow = ItemPrice::create($data);
                    addLog('create', 'Item Price Row', 'item_prices', $priceRow->id, null, $data);
                }

                return response()->json([
                    'success' => true,
                    'message' => "Size {$size} price updated successfully.",
                    'unit_price' => number_format($unitPrice, 2, '.', ''),
                ]);
            }

            $rules = [
                'finished_item_code' => 'required|string',
                'art_no'             => 'nullable|string',
                'selling_price'      => 'required|numeric|min:0',
                'effective_from'     => 'required|date_format:d-m-Y',
                'status'             => 'required|in:Active,Inactive',
                'sizes'              => 'nullable|array',
                'size_prices'        => 'required_with:sizes|array',
            ];

            if ($request->has('sizes') && is_array($request->sizes)) {
                foreach ($request->sizes as $sz) {
                    $rules["size_prices.{$sz}.selling_price"] = 'required|numeric|min:0';
                }
            }

            $messages = [
                '*.required' => 'This field is required.',
            ];
            $request->validate($rules, $messages);

            $effectiveFrom = Carbon::createFromFormat('d-m-Y', $request->effective_from)->format('Y-m-d');
            $finishedItemCode = $request->finished_item_code;
            $artNo = $request->art_no;
            $status = $request->status;

            if ($id) {
                $currentSize = $price->size;
                $selPrice = $request->selling_price;
                if ($request->has("size_prices.{$currentSize}.selling_price")) {
                    $selPrice = $request->input("size_prices.{$currentSize}.selling_price");
                }
                $unitPrice = $request->has("size_prices.{$currentSize}.unit_price") 
                    ? $request->input("size_prices.{$currentSize}.unit_price") 
                    : ($request->has('unit_price') && empty($currentSize) ? $request->unit_price : round($selPrice / 1.5, 2));
                
                $data = [
                    'finished_item_code' => $finishedItemCode,
                    'art_no'             => $artNo,
                    'selling_price'      => $selPrice,
                    'unit_price'         => $unitPrice,
                    'effective_from'     => $effectiveFrom,
                    'status'             => $status,
                    'updated_by'         => auth()->id() ?? 1,
                ];

                $price->update($data);
                addLog('update', 'Item Price', 'item_prices', $id, null, $data);

                $createdCount = 0;
                if ($request->has('sizes') && is_array($request->sizes)) {
                    foreach ($request->sizes as $sz) {
                        if ($sz == $currentSize) {
                            continue;
                        }

                        $selPrice = $request->input("size_prices.{$sz}.selling_price");
                        $unitPrice = $request->has("size_prices.{$sz}.unit_price") 
                            ? $request->input("size_prices.{$sz}.unit_price") 
                            : round($selPrice / 1.5, 2);
                        $sizeData = [
                            'finished_item_code' => $finishedItemCode,
                            'art_no'             => $artNo,
                            'size'               => $sz,
                            'selling_price'      => $selPrice,
                            'unit_price'         => $unitPrice,
                            'effective_from'     => $effectiveFrom,
                            'status'             => $status,
                            'created_by'         => auth()->id() ?? 1,
                        ];

                        $existing = ItemPrice::where('finished_item_code', $finishedItemCode)
                            ->where('art_no', $artNo)
                            ->where('size', $sz)
                            ->whereDate('effective_from', $effectiveFrom)
                            ->first();

                        if ($existing) {
                            $existing->update($sizeData);
                        } else {
                            ItemPrice::create($sizeData);
                        }
                        $createdCount++;
                    }
                }
                
                $checkedSizes = $request->has('sizes') && is_array($request->sizes) ? $request->sizes : [];
                if (empty($checkedSizes)) {
                    ItemPrice::where('finished_item_code', $finishedItemCode)
                        ->where('art_no', $artNo)
                        ->whereNotNull('size')
                        ->delete();
                        
                    // Ensure a base record exists
                    $baseExists = ItemPrice::where('finished_item_code', $finishedItemCode)->where('art_no', $artNo)->whereNull('size')->first();
                    if (!$baseExists) {
                        ItemPrice::create([
                            'finished_item_code' => $finishedItemCode,
                            'art_no'             => $artNo,
                            'size'               => null,
                            'selling_price'      => $request->selling_price,
                            'unit_price'         => $request->has('unit_price') ? $request->unit_price : round($request->selling_price / 1.5, 2),
                            'effective_from'     => $effectiveFrom,
                            'status'             => $status,
                            'created_by'         => auth()->id() ?? 1,
                        ]);
                    }
                } else {
                    ItemPrice::where('finished_item_code', $finishedItemCode)
                        ->where('art_no', $artNo)
                        ->whereNotNull('size')
                        ->whereNotIn('size', $checkedSizes)
                        ->delete();
                }

                // ALWAYS sync the base (size=null) record if it exists
                ItemPrice::where('finished_item_code', $finishedItemCode)
                    ->where('art_no', $artNo)
                    ->whereNull('size')
                    ->update([
                        'selling_price' => $request->selling_price,
                        'unit_price'    => $request->has('unit_price') ? $request->unit_price : round($request->selling_price / 1.5, 2),
                        'effective_from'=> $effectiveFrom,
                        'status'        => $status,
                        'updated_by'    => auth()->id() ?? 1,
                    ]);

                if ($createdCount > 0) {
                    $msg = 'Item Price updated and ' . $createdCount . ' other size prices added/updated successfully';
                } else {
                    $msg = 'Item Price updated successfully';
                }
            } else {
                if ($request->has('sizes') && is_array($request->sizes) && count($request->sizes) > 0) {
                    $createdCount = 0;
                    foreach ($request->sizes as $sz) {
                        $selPrice = $request->input("size_prices.{$sz}.selling_price");
                        $unitPrice = $request->has("size_prices.{$sz}.unit_price") 
                            ? $request->input("size_prices.{$sz}.unit_price") 
                            : round($selPrice / 1.5, 2);
                        $data = [
                            'finished_item_code' => $finishedItemCode,
                            'art_no'             => $artNo,
                            'size'               => $sz,
                            'selling_price'      => $selPrice,
                            'unit_price'         => $unitPrice,
                            'effective_from'     => $effectiveFrom,
                            'status'             => $status,
                            'created_by'         => auth()->id() ?? 1,
                        ];

                        $existing = ItemPrice::where('finished_item_code', $finishedItemCode)
                            ->where('art_no', $artNo)
                            ->where('size', $sz)
                            ->whereDate('effective_from', $effectiveFrom)
                            ->first();

                        if ($existing) {
                            $existing->update($data);
                        } else {
                            ItemPrice::create($data);
                        }
                        $createdCount++;
                    }
                    $msg = "Item Prices for {$createdCount} sizes added successfully";
                } else {
                    $data = [
                        'finished_item_code' => $finishedItemCode,
                        'art_no'             => $artNo,
                        'size'               => null,
                        'selling_price'      => $request->selling_price,
                        'unit_price'         => $request->has('unit_price') ? $request->unit_price : round($request->selling_price / 1.5, 2),
                        'effective_from'     => $effectiveFrom,
                        'status'             => $status,
                        'created_by'         => auth()->id() ?? 1,
                    ];

                    $newPrice = ItemPrice::create($data);
                    addLog('create', 'Item Price', 'item_prices', $newPrice->id, null, $data);
                    $msg = 'Item Price added successfully';
                }
            }

            return redirect('item_prices')->with('success', $msg);
        }

        $price = $id ? ItemPrice::findOrFail($id) : null;
        $allPrices = [];
        if ($price) {
            $allPrices = ItemPrice::where('finished_item_code', $price->finished_item_code)->where('art_no', $price->art_no)->get()->keyBy('size');
        }

        return view('item_prices.add', compact('price', 'allPrices'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete item-prices')) {
            return unauthorizedRedirect();
        }
        $price = ItemPrice::findOrFail($id);
        ItemPrice::where('finished_item_code', $price->finished_item_code)->where('art_no', $price->art_no)->delete();
        addLog('delete', 'Item Price Group', 'item_prices', $id, $price->toArray(), null);
        return redirect('item_prices')->with('success', 'Item Price group deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $price = ItemPrice::findOrFail($id);
        ItemPrice::where('finished_item_code', $price->finished_item_code)->where('art_no', $price->art_no)->update(['status' => $request->status]);
        addLog('update_status', 'Item Price Group Status', 'item_prices', $price->id, ['status' => $price->status], ['status' => $request->status]);
        return response()->json([
            'success' => true,
            'status'  => $request->status
        ]);
    }

    public function getArtNos(Request $request)
    {
        $itemCode = $request->item_code;
        
        $artNos1 = DB::table('stock_entry_items')
            ->where('finished_item_code', $itemCode)
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->whereNotNull('art_no')
            ->where('art_no', '!=', '')
            ->distinct()
            ->pluck('art_no')
            ->toArray();

        $artNos2 = DB::table('barcode_masters')
            ->where('item_code', $itemCode)
            ->whereNotNull('art_no')
            ->where('art_no', '!=', '')
            ->distinct()
            ->pluck('art_no')
            ->toArray();

        $artNos = array_values(array_unique(array_merge($artNos1, $artNos2)));

        $parts = explode('-', $itemCode);
        if (count($parts) >= 1) {
            $brandCode = trim($parts[0]);
            if ($brandCode !== '') {
                $brand = \DB::table('brands')->where('code', $brandCode)->first();
                if ($brand) {
                    $artNos3 = \DB::table('job_card_fabric_details')
                        ->join('job_card_entries', 'job_card_fabric_details.job_card_entry_id', '=', 'job_card_entries.id')
                        ->where('job_card_entries.brand_id', $brand->id)
                        ->whereNotNull('job_card_fabric_details.art_no')
                        ->where('job_card_fabric_details.art_no', '!=', '')
                        ->distinct()
                        ->pluck('job_card_fabric_details.art_no')
                        ->toArray();
                    
                    $artNos = array_values(array_unique(array_merge($artNos, $artNos3)));
                }
            }
        }

        return response()->json(['art_nos' => $artNos]);
    }

    public function searchItems(Request $request)
    {
        $term = $request->term;
        
        $results1 = DB::table('stock_entry_items')
            ->leftJoin('items', 'stock_entry_items.item_id', '=', 'items.id')
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at')
            ->where(function ($query) use ($term) {
                $query->where('stock_entry_items.finished_item_code', 'LIKE', "%{$term}%")
                    ->orWhere('items.name', 'LIKE', "%{$term}%")
                    ->orWhere('stock_entry_items.art_no', 'LIKE', "%{$term}%");
            })
            ->select(
                'stock_entry_items.item_id',
                'stock_entry_items.finished_item_code',
                'items.name as item_name'
            )
            ->distinct()
            ->get();

        $results2 = DB::table('barcode_masters')
            ->where(function ($query) use ($term) {
                $query->where('item_code', 'LIKE', "%{$term}%")
                    ->orWhere('item_name', 'LIKE', "%{$term}%")
                    ->orWhere('art_no', 'LIKE', "%{$term}%");
            })
            ->select(
                'item_code as finished_item_code',
                'item_name'
            )
            ->distinct()
            ->get();

        $formattedResults = [];
        $seenCodes = [];

        foreach ($results1 as $item) {
            if (!in_array($item->finished_item_code, $seenCodes)) {
                $seenCodes[] = $item->finished_item_code;
                $formattedResults[] = [
                    'id'    => $item->item_id,
                    'text'  => $item->finished_item_code . ($item->item_name ? ' - ' . $item->item_name : ''),
                    'code'  => $item->finished_item_code,
                    'name'  => $item->item_name ?? ''
                ];
            }
        }

        foreach ($results2 as $item) {
            if (!in_array($item->finished_item_code, $seenCodes)) {
                $seenCodes[] = $item->finished_item_code;
                
                $itemId = DB::table('stock_entry_items')
                    ->where('finished_item_code', $item->finished_item_code)
                    ->whereNull('deleted_at')
                    ->value('item_id');

                $formattedResults[] = [
                    'id'    => $itemId ?? '',
                    'text'  => $item->finished_item_code . ($item->item_name ? ' - ' . $item->item_name : ''),
                    'code'  => $item->finished_item_code,
                    'name'  => $item->item_name ?? ''
                ];
        }

        }

        // Dynamic Job Card Item Suggestions (Brand-Style Combinations)
        if (strlen($term) >= 2) {
            $brands = DB::table('brands')->whereNull('deleted_at')->where('status', 'Active')->get(['code', 'brand_name']);
            $styles = DB::table('styles')->whereNull('deleted_at')->where('status', 'Active')->get(['code', 'style_name']);
            
            foreach ($brands as $brand) {
                if (empty($brand->code)) continue;
                
                foreach ($styles as $style) {
                    if (empty($style->code)) continue;
                    
                    $baseCode = $brand->code . '-' . $style->code;
                    
                    // If the combination starts with the search term
                    if (stripos($baseCode, trim($term)) === 0) {
                        $codeFS = $baseCode . '-FS';
                        $codeHS = $baseCode . '-HS';
                        $baseName = trim($brand->brand_name . ' ' . $style->style_name);
                        
                        if (!in_array($codeFS, $seenCodes)) {
                            $seenCodes[] = $codeFS;
                            $formattedResults[] = [
                                'id'    => '',
                                'text'  => $codeFS . ' - ' . $baseName . ' F/S (New)',
                                'code'  => $codeFS,
                                'name'  => $baseName . ' F/S'
                            ];
                        }
                        
                        if (!in_array($codeHS, $seenCodes)) {
                            $seenCodes[] = $codeHS;
                            $formattedResults[] = [
                                'id'    => '',
                                'text'  => $codeHS . ' - ' . $baseName . ' H/S (New)',
                                'code'  => $codeHS,
                                'name'  => $baseName . ' H/S'
                            ];
                        }
                    }
                }
            }
        }

        return response()->json(array_slice($formattedResults, 0, 20));
    }

    public function exportExcel()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view item-prices')) {
            return unauthorizedRedirect();
        }
        return Excel::download(new ItemPricesExport, 'item_prices_' . date('Ymd_His') . '.xlsx');
    }

    public function import(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create item-prices')) {
            return unauthorizedRedirect();
        }

        $request->validate([
            'import_file' => 'required|mimes:csv,txt,xlsx,xls'
        ]);

        try {
            Excel::import(new ItemPricesImport, $request->file('import_file'));
            return redirect('item_prices')->with('success', 'Item prices imported successfully.');
        } catch (ValidationException $e) {
            return redirect('item_prices')->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Item price import failed', ['exception' => $e]);

            $message = config('app.debug')
                ? $e->getMessage()
                : 'Import failed. Please check the file and try again.';

            return redirect('item_prices')->with('error', $message);
        }
    }

    public function downloadSample()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view item-prices')) {
            return unauthorizedRedirect();
        }

        $headers = [
            'Finished Item Code',
            'Art No',
            'MRP 36',
            'Selling Price 36',
            'MRP 38',
            'Selling Price 38',
            'MRP 40',
            'Selling Price 40',
            'MRP 42',
            'Selling Price 42',
            'MRP 44',
            'Selling Price 44',
            'MRP 46',
            'Selling Price 46',
            'MRP 48',
            'Selling Price 48',
            'MRP 50',
            'Selling Price 50',
            'Effective From',
            'Status',
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);
            // fputcsv($file, [
            //     'CDS-PRNT',
            //     'CDS30906',
            //     '499.00',
            //     '',
            //     date('d-m-Y'),
            //     'Active',
            // ]);
            fclose($file);
        };

        return response()->streamDownload($callback, 'Item_Prices_Sample.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
