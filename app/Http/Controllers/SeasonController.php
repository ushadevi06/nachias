<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\JobCardEntry;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view seasons')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $seasons = Season::latest()->get();

            $data = [];
            $i = 1;

            foreach ($seasons as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input season-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit seasons')) {
                    $action .= '<a href="' . url('seasons/add/' . $row->id) . '" class="btn btn-edit">
                                    <i class="icon-base ri ri-edit-box-line"></i>
                                </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete seasons')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('seasons/delete/' . $row->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'name'        => $row->name,
                    'season_code' => $row->season_code,
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('seasons.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit seasons')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create seasons')) {
                return unauthorizedRedirect();
            }
        }

        $season = $id ? Season::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'name'   => 'required|string|min:3|max:50|unique:seasons,name,' . $id . ',id,deleted_at,NULL',
                'season_code' => 'nullable|string|max:50|regex:/^(?!0+$).*$/',
                'status' => 'required|in:Active,Inactive'
            ];
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
                'season_code.regex' => 'Season Code cannot be zero.',
            ];
            $request->validate($rules, $messages);

            $data = $request->only(['name', 'season_code', 'status']);

            if ($id) {
                $data['updated_by'] = auth()->id();
                Season::where('id', $id)->update($data);
                addLog('update', 'Season', 'seasons', $id, null, $data);
                $msg = 'Season updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $newSeason = Season::create($data);
                addLog('create', 'Season', 'seasons', $newSeason->id, null, $data);
                $msg = 'Season added successfully';
            }

            return redirect('seasons')->with('success', $msg);
        }

        return view('seasons.add', compact('season'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete seasons')) {
            return unauthorizedRedirect();
        }

        if (JobCardEntry::where('season_id', $id)->exists()) {
            return redirect('seasons')->with('danger', "This Season is currently referenced in Job Cards and cannot be deleted.");
        }

        $season = Season::findOrFail($id);
        $oldData = $season->toArray();
        $season->delete();
        addLog('delete', 'Season', 'seasons', $id, $oldData, null);
        return redirect('seasons')->with('success', 'Season deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $season = Season::findOrFail($id);
        $oldData = $season->toArray();
        $season->status = $request->status;
        $season->save();
        $newData = $season->toArray();
        addLog('update_status', 'Season Status', 'seasons', $season->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status'  => $season->status
        ]);
    }
}
