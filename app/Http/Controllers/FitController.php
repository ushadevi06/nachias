<?php

namespace App\Http\Controllers;

use App\Models\Fit;
use Illuminate\Http\Request;

class FitController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view fits')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $fits = Fit::latest()->get();

            $data = [];
            $i = 1;

            foreach ($fits as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input fit-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit fits')) {
                    $action .= '<a href="' . url('fits/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete fits')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('fits/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'fit_name'    => $row->fit_name,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('fits.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit fits')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create fits')) {
                return unauthorizedRedirect();
            }
        }

        $fit = $id ? Fit::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'fit_name' => 'required|string|max:255|unique:fits,fit_name,' . $id . ',id,deleted_at,NULL',
                'status'   => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['fit_name', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                Fit::where('id', $id)->update($data);
                addLog('update', 'Fit', 'fits', $id, null, $data);
                $msg = 'Fit updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newFit = Fit::create($data);
                addLog('create', 'Fit', 'fits', $newFit->id, null, $data);
                $msg = 'Fit added successfully';
            }

            return redirect('fits')->with('success', $msg);
        }

        return view('fits.add', compact('fit'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete fits')) {
            return unauthorizedRedirect();
        }

        $fit = Fit::findOrFail($id);
        $oldData = $fit->toArray();
        $fit->delete();
        addLog('delete', 'Fit', 'fits', $id, $oldData, null);
        return redirect('fits')->with('success', 'Fit deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $fit = Fit::findOrFail($id);
        $oldData = $fit->toArray();
        $fit->status = $request->status;
        $fit->save();
        $newData = $fit->toArray();
        addLog('update_status', 'Fit Status', 'fits', $fit->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $fit->status
        ]);
    }
}
