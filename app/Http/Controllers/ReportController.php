<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StockEntryItem;

class ReportController extends Controller
{
    public function customer_reports(){
        return view('reports/customer_report');
    }
    public function sale_reports(){
        return view('reports/sale_report');
    }
    public function stock_reports(){
        $stockBalances = StockEntryItem::selectRaw('
            stock_type, 
            store_category_id, 
            store_location_id, 
            raw_material_id, 
            item_id, 
            SUM(qty_in - qty_out) as current_stock,
            SUM((qty_in - qty_out) * price) as total_amount
        ')
        ->groupBy('stock_type', 'store_category_id', 'store_location_id', 'raw_material_id', 'item_id')
        ->havingRaw('SUM(qty_in - qty_out) > 0')
        ->with(['storeCategory', 'storeLocation', 'rawMaterial', 'item'])
        ->get();

        return view('reports/stock_report', compact('stockBalances'));
    }
    public function daily_production_reports(){
        return view('reports/daily_production_report');
    }
    public function order_reports(){
        return view('reports/order_report');
    }
    public function employee_reports(){
        return view('reports/employee_report');
    }
}
