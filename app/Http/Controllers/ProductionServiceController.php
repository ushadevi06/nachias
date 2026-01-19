<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\ProductionService; // Model doesn't exist yet, purely for UI routing

class ProductionServiceController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            // Placeholder data for UI demonstration
             $data = [
                [
                    'DT_RowIndex' => 1,
                    'service_name' => 'Button Operator',
                    'service_code' => 'BUT - OPR',
                    'status' => '<span class="badge bg-label-success">Active</span>',
                    'action' => '<div class="button-box">
                                    <a href="#" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                </div>'
                ],
                [
                    'DT_RowIndex' => 2,
                    'service_name' => 'Button & Kaja Operation',
                    'service_code' => 'BUT,KAJA - OPR',
                    'status' => '<span class="badge bg-label-success">Active</span>',
                    'action' => '<div class="button-box">
                                    <a href="#" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                </div>'
                ],
                [
                   'DT_RowIndex' => 3,
                   'service_name' => 'Collar Attach',
                   'service_code' => 'CLR-ATT',
                   'status' => '<span class="badge bg-label-success">Active</span>',
                   'action' => '<div class="button-box">
                                   <a href="#" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                               </div>'
               ],
            ];
            return response()->json(['data' => $data]);
        }
        return view('production_services/view');
    }

    public function add(Request $request, $id = null) {
        if ($request->isMethod('post')) {
            // Validation and logic would go here
             return redirect('production_services')->with('success', 'Production Service saved successfully (UI Simulation)');
        }
        $service = null; // Placeholder for edit interaction
        return view('production_services/add', compact('service'));
    }

    // public function destroy($id) { ... } // Placeholder
    
    public function status($id) {
         return response()->json(['success' => true]);
    }
}
