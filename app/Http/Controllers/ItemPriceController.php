<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemPricesExport;

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
                ->whereNull('item_prices.deleted_at')
                ->select(
                    'item_prices.*',
                    DB::raw('COALESCE(items.name, "-") as item_display_name')
                )
                ->orderBy('item_prices.id', 'desc')
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

                $data[] = [
                    'DT_RowIndex'    => $i++,
                    'item_name'      => ($row->item_display_name && $row->item_display_name != '-') 
                                        ? $row->finished_item_code . ' - ' . $row->item_display_name 
                                        : $row->finished_item_code,
                    'art_no'         => $row->art_no ?? '-',
                    'selling_price'  => number_format($row->selling_price, 2),
                    'unit_price'     => number_format($row->unit_price, 2),
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
            $rules = [
                'finished_item_code' => 'required|string',
                'art_no'             => 'nullable|string',
                'selling_price'      => 'required|numeric|min:0',
                'effective_from'     => 'required|date_format:d-m-Y',
                'status'             => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['finished_item_code', 'art_no', 'selling_price', 'status']);
            $data['unit_price'] = $request->selling_price / 1.5;
            $data['effective_from'] = Carbon::createFromFormat('d-m-Y', $request->effective_from)->format('Y-m-d');

            if ($id) {
                $data['updated_by'] = auth()->id();
                ItemPrice::where('id', $id)->update($data);
                addLog('update', 'Item Price', 'item_prices', $id, null, $data);
                $msg = 'Item Price updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newPrice = ItemPrice::create($data);
                addLog('create', 'Item Price', 'item_prices', $newPrice->id, null, $data);
                $msg = 'Item Price added successfully';
            }

            return redirect('item_prices')->with('success', $msg);
        }

        return view('item_prices.add', compact('price'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete item-prices')) {
            return unauthorizedRedirect();
        }
        $price = ItemPrice::findOrFail($id);
        $oldData = $price->toArray();
        $price->delete();
        addLog('delete', 'Item Price', 'item_prices', $id, $oldData, null);
        return redirect('item_prices')->with('success', 'Item Price deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $price = ItemPrice::findOrFail($id);
        $oldData = $price->toArray();
        $price->status = $request->status;
        $price->save();
        $newData = $price->toArray();
        addLog('update_status', 'Item Price Status', 'item_prices', $price->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $price->status
        ]);
    }

    public function getArtNos(Request $request)
    {
        $itemCode = $request->item_code;
        $artNos = DB::table('stock_entry_items')
            ->where('finished_item_code', $itemCode)
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->whereNotNull('art_no')
            ->where('art_no', '!=', '')
            ->distinct()
            ->pluck('art_no');

        return response()->json(['art_nos' => $artNos]);
    }

    public function searchItems(Request $request)
    {
        $term = $request->term;
        $results = DB::table('stock_entry_items')
            ->leftJoin('items', 'stock_entry_items.item_id', '=', 'items.id')
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at')
            ->where(function ($query) use ($term) {
                $query->where('stock_entry_items.finished_item_code', 'LIKE', "%{$term}%")
                    ->orWhere('items.name', 'LIKE', "%{$term}%");
            })
            ->select(
                'stock_entry_items.item_id',
                'stock_entry_items.finished_item_code',
                'items.name as item_name'
            )
            ->distinct()
            ->limit(20)
            ->get();

        $formattedResults = [];
        foreach ($results as $item) {
            $formattedResults[] = [
                'id'    => $item->item_id,
                'text'  => $item->finished_item_code . ($item->item_name ? ' - ' . $item->item_name : ''),
                'code'  => $item->finished_item_code,
                'name'  => $item->item_name ?? ''
            ];
        }

        return response()->json($formattedResults);
    }

    public function exportExcel()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view item-prices')) {
            return unauthorizedRedirect();
        }
        return Excel::download(new ItemPricesExport, 'item_prices_' . date('Ymd_His') . '.xlsx');
    }
}
