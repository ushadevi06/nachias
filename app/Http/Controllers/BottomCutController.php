<?php

namespace App\Http\Controllers;

use App\Models\BottomCut;
use Illuminate\Http\Request;

class BottomCutController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view bottom cuts')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $bottomCuts = BottomCut::latest()->get();

            $data = [];
            $i = 1;

            foreach ($bottomCuts as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input bottom-cut-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit bottom cuts')) {
                    $action .= '<a href="' . url('bottom_cuts/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete bottom cuts')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('bottom_cuts/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex'      => $i++,
                    'bottom_cut_name'  => $row->bottom_cut_name,
                    'status'           => $status,
                    'action'           => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('bottom_cuts.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit bottom cuts')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create bottom cuts')) {
                return unauthorizedRedirect();
            }
        }

        $bottomCut = $id ? BottomCut::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'bottom_cut_name' => 'required|string|max:255|unique:bottom_cuts,bottom_cut_name,' . $id . ',id,deleted_at,NULL',
                'status'          => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['bottom_cut_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                BottomCut::where('id', $id)->update($data);
                addLog('update', 'Bottom Cut', 'bottom_cuts', $id, null, $data);
                $msg = 'Bottom Cut updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newBottomCut = BottomCut::create($data);
                addLog('create', 'Bottom Cut', 'bottom_cuts', $newBottomCut->id, null, $data);
                $msg = 'Bottom Cut added successfully';
            }

            return redirect('bottom_cuts')->with('success', $msg);
        }

        return view('bottom_cuts.add', compact('bottomCut'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete bottom cuts')) {
            return unauthorizedRedirect();
        }

        $bottomCut = BottomCut::findOrFail($id);
        $oldData = $bottomCut->toArray();
        $bottomCut->delete();
        addLog('delete', 'Bottom Cut', 'bottom_cuts', $id, $oldData, null);
        return redirect('bottom_cuts')->with('success', 'Bottom Cut deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $bottomCut = BottomCut::findOrFail($id);
        $oldData = $bottomCut->toArray();
        $bottomCut->status = $request->status;
        $bottomCut->save();
        $newData = $bottomCut->toArray();
        addLog('update_status', 'Bottom Cut Status', 'bottom_cuts', $bottomCut->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $bottomCut->status
        ]);
    }
}
