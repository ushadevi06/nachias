<?php

namespace App\Http\Controllers;

use App\Models\CoreMaterialPlannerSetting;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoreMaterialPlannerSettingController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view settings')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $settings = CoreMaterialPlannerSetting::with('brand')->latest()->get();

            $data = [];
            $i = 1;

            foreach ($settings as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input core-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit settings')) {
                    $action .= '<a href="' . url('core-material-settings/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete settings')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('core-material-settings/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'        => $i++,
                    'art_no'             => $row->art_no,
                    'brand_name'         => $row->brand ? $row->brand->brand_name : '-',
                    'daily_consumption'  => number_format($row->daily_consumption, 2) . ' M',
                    'supplier_lead_time' => $row->supplier_lead_time . ' Days',
                    'safety_stock'       => number_format($row->safety_stock, 2) . ' M',
                    'status'             => $status,
                    'action'             => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('core_material_planner_settings.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit settings')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create settings')) {
                return unauthorizedRedirect();
            }
        }

        $setting = $id ? CoreMaterialPlannerSetting::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'art_no'             => 'required|string|unique:core_material_planner_settings,art_no,' . $id . ',id,deleted_at,NULL',
                'brand_id'           => 'nullable|integer',
                'daily_consumption'  => 'required|numeric|min:0',
                'supplier_lead_time' => 'required|integer|min:0',
                'safety_stock'       => 'required|numeric|min:0',
                'status'             => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only([
                'art_no',
                'brand_id',
                'daily_consumption',
                'supplier_lead_time',
                'safety_stock',
                'status'
            ]);

            if ($id) {
                $data['updated_by'] = auth()->id();
                $setting->update($data);
                addLog('update', 'Core Material Planner Master', 'core_material_planner_settings', $id, null, $data);
                $msg = 'Core Material Planner Master updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newSetting = CoreMaterialPlannerSetting::create($data);
                addLog('create', 'Core Material Planner Master', 'core_material_planner_settings', $newSetting->id, null, $data);
                $msg = 'Core Material Planner Master added successfully';
            }

            return redirect('core-material-settings')->with('success', $msg);
        }

        // Get distinct art numbers from Fabric stock items
        $artNumbers = DB::table('stock_entry_items as sei')
            ->leftJoin('brands as b', 'sei.brand_id', '=', 'b.id')
            ->where('sei.store_category_id', 1)
            ->whereNull('sei.deleted_at')
            ->whereNotNull('sei.art_no')
            ->where('sei.art_no', '!=', '')
            ->select('sei.art_no', 'sei.brand_id', DB::raw('MAX(b.brand_name) as brand_name'))
            ->groupBy('sei.art_no', 'sei.brand_id')
            ->get();

        $brands = Brand::whereNull('deleted_at')->orderBy('brand_name')->get();

        return view('core_material_planner_settings.add', compact('setting', 'artNumbers', 'brands'));
    }

    public function delete($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete settings')) {
            return unauthorizedRedirect();
        }

        $setting = CoreMaterialPlannerSetting::findOrFail($id);
        $oldData = $setting->toArray();
        $setting->delete();
        addLog('delete', 'Core Material Planner Master', 'core_material_planner_settings', $id, $oldData, null);

        return redirect('core-material-settings')->with('success', 'Core Material Planner Master deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $setting = CoreMaterialPlannerSetting::findOrFail($id);
        $oldData = $setting->toArray();
        $setting->status = $request->status;
        $setting->save();
        $newData = $setting->toArray();
        addLog('update_status', 'Core Material Planner Status', 'core_material_planner_settings', $setting->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $setting->status
        ]);
    }

    public function getArtNoDetails($artNo)
    {
        $artItem = DB::table('stock_entry_items')
            ->where('art_no', urldecode($artNo))
            ->where('store_category_id', 1)
            ->whereNull('deleted_at')
            ->whereNotNull('brand_id')
            ->first();

        return response()->json([
            'brand_id' => $artItem ? $artItem->brand_id : null
        ]);
    }
}
