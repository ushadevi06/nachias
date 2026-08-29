<?php

namespace App\Http\Controllers;

use App\Models\FgMinStock;
use App\Models\StockEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FgMinStockController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view fg-min-stocks')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $fgMinStocks = FgMinStock::with('stockEntryItem.item')->latest()->get();

            $data = [];
            $i = 1;

            foreach ($fgMinStocks as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input fg-min-stock-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit fg-min-stocks')) {
                    $action .= '<a href="' . url('fg-min-stocks/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete fg-min-stocks')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('fg-min-stocks/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $stockItem = $row->stockEntryItem;
                $itemName = $stockItem ? ($stockItem->finished_item_code ?: ($stockItem->item ? $stockItem->item->name : 'Unknown Item')) : '-';
                
                $data[] = [
                    'DT_RowIndex' => $i++,
                    'fg_name'     => $itemName,
                    'art_no'      => $stockItem->art_no ?? '-',
                    'size'        => $stockItem->size ?? '-',
                    'sleeve'      => $stockItem->sleeve_type ?? '-',
                    'min_stock'   => $row->min_stock,
                    'max_stock'   => $row->max_stock ?? '0.00',
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('fg_min_stocks.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit fg-min-stocks')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create fg-min-stocks')) {
                return unauthorizedRedirect();
            }
        }

        $fgMinStock = $id ? FgMinStock::with('stockEntryItem.item')->findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'stock_entry_item_id' => 'required|exists:stock_entry_items,id|unique:fg_min_stocks,stock_entry_item_id,' . $id . ',id,deleted_at,NULL',
                'min_stock'           => 'required|numeric|min:0',
                'max_stock'           => 'nullable|numeric|min:0',
                'status'              => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                'stock_entry_item_id.unique' => 'Minimum stock for this Finished Good already exists.',
                '*.min'      => 'Value must be at least :min.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['stock_entry_item_id', 'min_stock', 'max_stock', 'status']);
            $data['max_stock'] = $data['max_stock'] ?? 0;

            if ($id) {
                $data['updated_by'] = auth()->id();
                FgMinStock::where('id', $id)->update($data);
                addLog('update', 'FG Min Stock', 'fg_min_stocks', $id, null, $data);
                $msg = 'Finished Goods Minimum Stock updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newFgMinStock = FgMinStock::create($data);
                addLog('create', 'FG Min Stock', 'fg_min_stocks', $newFgMinStock->id, null, $data);
                $msg = 'Finished Goods Minimum Stock added successfully';
            }

            return redirect('fg-min-stocks')->with('success', $msg);
        }

        return view('fg_min_stocks.add', compact('fgMinStock'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete fg-min-stocks')) {
            return unauthorizedRedirect();
        }
        $fgMinStock = FgMinStock::findOrFail($id);
        $oldData = $fgMinStock->toArray();
        $fgMinStock->delete();
        addLog('delete', 'FG Min Stock', 'fg_min_stocks', $id, $oldData, null);
        return redirect('fg-min-stocks')->with('success', 'Finished Goods Minimum Stock deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $fgMinStock = FgMinStock::findOrFail($id);
        $oldData = $fgMinStock->toArray();
        $fgMinStock->status = $request->status;
        $fgMinStock->save();
        $newData = $fgMinStock->toArray();
        addLog('update_status', 'FG Min Stock Status', 'fg_min_stocks', $fgMinStock->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $fgMinStock->status
        ]);
    }

    public function searchStockItems(Request $request)
    {
        $term = $request->get('term', '');
        $currentFgMinStockId = $request->get('current_id', null);

        $existingItemIds = FgMinStock::whereNull('deleted_at')
            ->when($currentFgMinStockId, function($q) use ($currentFgMinStockId) {
                $q->where('id', '!=', $currentFgMinStockId);
            })
            ->pluck('stock_entry_item_id')
            ->toArray();

        $query = StockEntryItem::with('item')
            ->where(function ($q) {
                $q->where('store_category_id', 2)->orWhere('stock_type', 'finished_goods');
            })
            ->whereNotNull('art_no')
            ->where(function ($q) use ($term) {
                $q->where('art_no', 'like', "%{$term}%")
                  ->orWhere('size', 'like', "%{$term}%")
                  ->orWhere('sleeve_type', 'like', "%{$term}%")
                  ->orWhere('finished_item_code', 'like', "%{$term}%")
                  ->orWhereHas('item', function ($qItem) use ($term) {
                      $qItem->where('name', 'like', "%{$term}%");
                  });
            })
            ->select('id', 'item_id', 'finished_item_code', 'art_no', 'size', 'sleeve_type')
            ->limit(50)
            ->get();

        $unique = $query->unique(function ($item) {
            $itemIdentifier = $item->finished_item_code ?: $item->item_id;
            return $itemIdentifier . '-' . $item->art_no . '-' . $item->size . '-' . $item->sleeve_type;
        });

        $results = [];
        foreach ($unique as $row) {
            $itemName = $row->finished_item_code ?: ($row->item ? $row->item->name : 'Unknown Item');
            $alreadyExists = in_array($row->id, $existingItemIds);

            $label = "Art No: {$row->art_no} | Item: {$itemName} | Size: {$row->size}";
            if ($row->sleeve_type && $row->sleeve_type !== '-') {
                $label .= " | Sleeve: {$row->sleeve_type}";
            }
            if ($alreadyExists) {
                $label .= " (Already Exists)";
            }

            $results[] = [
                'id' => $row->id,
                'label' => $label,
                'value' => $row->art_no,
                'item_name' => $itemName,
                'art_no' => $row->art_no,
                'size' => $row->size,
                'sleeve_type' => $row->sleeve_type ?? '-',
                'already_exists' => $alreadyExists
            ];
        }

        return response()->json(array_values($results));
    }
}
