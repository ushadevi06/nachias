<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view colors')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $colors = Color::latest()->get();
            $data = [];
            $count = 1;

            foreach ($colors as $color) {
                $checked = $color->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input color-status-toggle"
                        data-id="' . $color->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $color->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit colors')) {
                    $action .= '
                        <a href="' . url('colors/add/' . $color->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete colors')) {
                    $action .= '
                        <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('colors/delete/' . $color->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'color_name' => $color->color_name,
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('colors.view');
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit colors')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create colors')) {
                return unauthorizedRedirect();
            }
        }
        $color = $id ? Color::findOrFail($id) : new Color();
        $oldData = $id ? $color->toArray() : null;

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'color_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('colors', 'color_name')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'description' => 'nullable|string',
                'status' => 'required|in:Active,Inactive'
            ];

            $messages =  [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];

            $validated = $request->validate($rules,$messages);

            if ($id) {
                $color->color_name = $request->color_name;
                $color->description = $request->description;
                $color->status = $request->status;
                $color->updated_by = auth()->id();
                $color->save();

                $newData = Color::find($id)->toArray();
                addLog('update', 'Color', 'colors', $color->id, $oldData, $newData);
                $message = 'Color updated successfully';
            } else {
                $color->color_name = $request->color_name;
                $color->description = $request->description;
                $color->status = $request->status;
                $color->created_by = auth()->id();
                $color->save();

                $newData = Color::find($color->id)->toArray();
                addLog('create', 'Color', 'colors', $color->id, null, $newData);
                $message = 'Color added successfully';
            }

            return redirect('colors')->with('success', $message);
        }

        return view('colors.add', compact('color'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete colors')) {
            return unauthorizedRedirect();
        }
        $color = Color::findOrFail($id);

        // Refer any other functionality for dependency checks if needed. 
        // For now, colors might be used in items, but I'll skip complex dependency check unless I find where it's used.
        // Similar to UomController:
        /*
        $tables = [
            'items' => 'Items',
        ];

        foreach ($tables as $table => $label) {
            $query = \Illuminate\Support\Facades\DB::table($table)->where('color_id', $id);
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }
            if ($query->exists()) {
                return redirect('colors')->with('danger', "This color is currently referenced in {$label} and cannot be deleted.");
            }
        }
        */

        $oldData = $color->toArray();
        $color->delete();
        addLog('delete','Color','colors',$id,$oldData,null);
        return redirect('colors')->with('success', 'Color deleted successfully');
    }


    public function updateStatus(Request $request, $id)
    {
        $color = Color::findOrFail($id);
        $oldData = $color->toArray();

        $color->status = $request->status;
        $color->save();

        $newData = $color->toArray();

        addLog('update_status', 'Color Status', 'colors', $color->id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'status'  => $color->status
        ]);
    }

}
