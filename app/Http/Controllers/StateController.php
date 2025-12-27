<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\City;

class StateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $states = State::orderBy('id','desc')->get();
            $data = [];
            $count = 1;

            foreach ($states as $state) {

                $checked = $state->status === 'Active' ? 'checked' : '';

                $statusSwitch = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input state-status-toggle"
                        data-id="' . $state->id . '"
                        ' . $checked . '>

                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $state->id . '"></div>
            ';
                $actionBtn = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('edit states')) {
                    $actionBtn .= '
                        <a href="' . url('states/add/' . $state->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete states')) {
                    $actionBtn .= '
                        <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('states/delete/' . $state->id) . '\')">
                            <i class="icon-base ri ri-delete-bin-line"></i>
                        </a>';
                }
                $actionBtn .= '</div>';


                $data[] = [
                    'DT_RowIndex' => $count++,
                    'state_code' => $state->state_code,
                    'state_name' => $state->state_name,
                    'status'     => $statusSwitch,
                    'action'     => $actionBtn
                ];
            }

            return response()->json([
                "data" => $data
            ]);
        }

        return view('states.index');
    }



    public function add(Request $request, $id = null)
    {
        $state = $id ? State::findOrFail($id) : new State();
        $oldData = $id ? $state->toArray() : null;

        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'state_code' => [
                    'required',
                    'max:10',
                    'unique:states,state_code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'state_name' => [
                    'required',
                    'max:100',
                    'unique:states,state_name,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'status' => 'required|in:Active,Inactive'
            ], [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ]);

            if ($id) {
                $validated['updated_by'] = auth()->id();
                $state->update($validated);
                $newData = State::find($id)->toArray();
                addLog('update', 'State', 'states', $state->id, $oldData, $newData);
                return redirect('states')->with('success', 'State updated successfully.');
            } else {
                $validated['created_by'] = auth()->id();
                $newState = State::create($validated);
                $newData = State::find($newState->id)->toArray();
                addLog('create', 'State', 'states', $newState->id, null, $newData);
                return redirect('states')->with('success', 'State added successfully.');
            }
        }

        return view('states.add', compact('state', 'id'));
    }

    public function updateStatus(Request $request, $id)
    {
        $state = State::findOrFail($id);
        $oldData = $state->toArray();
        $state->status = $request->status;
        $state->save();
        $newData = $state->toArray();
        addLog('update_status', 'State Status', 'states', $state->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $state = State::findOrFail($id);
        $tables = [
            'cities' => 'Cities',
            'suppliers' => 'Suppliers',
            'customers' => 'Customers',
            'employees' => 'Employees',
            'zones' => 'Zones',
            'sales_agents' => 'Sales Agents',
            'purchase_commission_agents' => 'Purchase Commission Agents',
            'service_providers' => 'Service Providers',
            'places' => 'Places',
            'users' => 'Users',
            'settings' => 'Settings',
        ];

        foreach ($tables as $table => $label) {
            $query = \Illuminate\Support\Facades\DB::table($table)->where('state_id', $id);
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }
            if ($query->exists()) {
                session()->flash('danger', "This state is currently referenced in {$label} and cannot be deleted..");
                return redirect('states');
            }
        }

        $oldData = $state->toArray();
        $state->delete();
        addLog('delete', 'State', 'states', $id, $oldData, null);
        session()->flash('success', 'State deleted successfully');  
        return redirect('states');
    }
}
