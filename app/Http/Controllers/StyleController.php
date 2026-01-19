<?php

namespace App\Http\Controllers;

use App\Models\Style;
use Illuminate\Http\Request;

class StyleController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view styles')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $styles = Style::latest()->get();

            $data = [];
            $i = 1;

            foreach ($styles as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input style-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit styles')) {
                    $action .= '<a href="' . url('styles/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete styles')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('styles/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'style_name' => $row->style_name,
                    'code'       => $row->code,
                    'status'     => $status,
                    'action'     => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('styles.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit styles')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create styles')) {
                return unauthorizedRedirect();
            }
        }

        $style = $id ? Style::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'style_name' => 'required|string|max:255|unique:styles,style_name,' . $id . ',id,deleted_at,NULL',
                'code'       => 'required|string|max:50|unique:styles,code,' . $id . ',id,deleted_at,NULL',
                'status'     => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['style_name', 'code', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                Style::where('id', $id)->update($data);
                addLog('update', 'Style', 'styles', $id, null, $data);
                $msg = 'Style updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newStyle = Style::create($data);
                addLog('create', 'Style', 'styles', $newStyle->id, null, $data);
                $msg = 'Style added successfully';
            }

            return redirect('styles')->with('success', $msg);
        }

        return view('styles.add', compact('style'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete styles')) {
            return unauthorizedRedirect();
        }

        $style = Style::findOrFail($id);
        $oldData = $style->toArray();
        $style->delete();
        addLog('delete', 'Style', 'styles', $id, $oldData, null);
        return redirect('styles')->with('success', 'Style deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $style = Style::findOrFail($id);
        $oldData = $style->toArray();
        $style->status = $request->status;
        $style->save();
        $newData = $style->toArray();
        addLog('update_status', 'Style Status', 'styles', $style->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $style->status
        ]);
    }
}
