<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UomController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $uoms = Uom::latest()->get();
            $data = [];
            $count = 1;

            foreach ($uoms as $uom) {
                $checked = $uom->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input uom-status-toggle"
                        data-id="' . $uom->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $uom->id . '"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit uoms')) {
                    $action .= '
                        <a href="' . url('uoms/add/' . $uom->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete uoms')) {
                    $action .= '
                        <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('uoms/delete/' . $uom->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'uom_code' => $uom->uom_code,
                    'uom_name' => $uom->uom_name,
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('uom.view');
    }


    public function add($id = null)
    {
        $uom = $id ? Uom::findOrFail($id) : new Uom();
        $oldData = $id ? $uom->toArray() : null;

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'uom_code' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('uoms', 'uom_code')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'uom_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('uoms', 'uom_name')
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
                $uom->uom_code = $request->uom_code;
                $uom->uom_name = $request->uom_name;
                $uom->description = $request->description;
                $uom->status = $request->status;
                $uom->updated_by = auth()->id();
                $uom->save();

                $newData = Uom::find($id)->toArray();
                addLog('update', 'UOM', 'uoms', $uom->id, $oldData, $newData);
                $message = 'UOM updated successfully';
            } else {
                $validated['created_by'] = auth()->id();
                $uom->uom_code = $request->uom_code;
                $uom->uom_name = $request->uom_name;
                $uom->description = $request->description;
                $uom->status = $request->status;
                $uom->created_by = auth()->id();
                $uom->save();

                $newData = Uom::find($uom->id)->toArray();
                addLog('create', 'UOM', 'uoms', $uom->id, null, $newData);
                $message = 'UOM added successfully';
            }

            return redirect('uoms')->with('success', $message);
        }

        return view('uom.add', compact('uom'));
    }

    public function destroy($id)
    {
        $uom = Uom::findOrFail($id);
        $oldData = $uom->toArray();
        $uom->delete();
        addLog('delete','UOM','uoms',$id,$oldData,null);
        return redirect('uoms')->with('success', 'UOM deleted successfully');
    }


    public function updateStatus(Request $request, $id)
    {
        $uom = Uom::findOrFail($id);
        $oldData = $uom->toArray();

        $uom->status = $request->status;
        $uom->save();

        $newData = $uom->toArray();

        addLog('update', 'UOM Status', 'uoms', $uom->id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'status'  => $uom->status
        ]);
    }

}
