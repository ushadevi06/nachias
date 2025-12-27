<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\City;
use App\Models\State;
use App\Models\Place;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = City::with('state')->orderBy('id','desc');
            if (!empty($request->state)) {
                $query->whereHas('state', function ($q) use ($request) {
                    $q->where('state_code', $request->state);
                });
            }
            $cities = $query->get();
            $data = [];
            $count = 1;

            foreach ($cities as $city) {
                $checked = $city->status === 'Active' ? 'checked' : '';
                $statusSwitch = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input city-status-toggle"
                        data-id="' . $city->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $city->id . '"></div>
            ';

                $actionBtn = '';
                if (auth()->id() == 1 || auth()->user()->can('edit cities')) {
                    $actionBtn .= '
                        <a href="' . url('cities/add/' . $city->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>
                    ';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete cities')) {
                    $actionBtn .= '
                    <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('cities/delete/' . $city->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </a>';
                }

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'state'      => $city->state->state_name ?? '-',
                    'city_name'  => $city->city_name ?? '-',
                    'city_code'  => $city->city_code ?? '-',
                    'status'     => $statusSwitch,
                    'action'     => $actionBtn,
                ];
            }

            return response()->json([
                "data" => $data
            ]);
        }

        return view('cities.index');
    }

    public function add(Request $request, $id = null)
    {
        $city = $id ? City::findOrFail($id) : new City();
        $oldData = $id ? $city->toArray() : null;
        if ($request->isMethod('post')) {

            $validated = $request->validate([
                'state_id'   => 'required|exists:states,id',
                'city_code' => [
                    'nullable',
                    'max:10',
                    Rule::unique('cities', 'city_code')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'city_name' => [
                    'required',
                    'max:100',
                    Rule::unique('cities', 'city_name')
                        ->ignore($id)
                        ->whereNull('deleted_at')
                ],
                'status'    => 'required|in:Active,Inactive',
            ], [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
            ]);

            if ($id) {
                $validated['updated_by'] = auth()->id();
                $city->update($validated);
                $newData = City::find($id)->toArray();
                addLog('update', 'City', 'cities', $city->id, $oldData, $newData);
                return redirect('cities')->with('success', 'City updated successfully.');
            } else {
                $validated['created_by'] = auth()->id();
                $newCity = City::create($validated);
                $newData = City::find($newCity->id)->toArray();
                addLog('create', 'City', 'cities', $newCity->id, null, $newData);
                return redirect('cities')->with('success', 'City added successfully.');
            }
        }

        $city   = $id ? City::findOrFail($id) : null;
        $states = State::where('status', 'Active')->get();
        return view('cities.add', compact('city', 'states', 'id'));
    }
    public function updateStatus(Request $request, $id)
    {
        $city = City::findOrFail($id);
        $oldData = $city->toArray();
        $city->status = $request->status;
        $city->save();
        $newData = $city->toArray();
        addLog('update_status', 'City Status', 'cities', $city->id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $tables = [
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
            $query = \Illuminate\Support\Facades\DB::table($table)->where('city_id', $id);
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }
            if ($query->exists()) {
                session()->flash('danger', "This city is currently referenced in {$label} and cannot be deleted.");
                return redirect('cities');
            }
        }

        $oldData = $city->toArray();
        $city->delete();
        addLog('delete', 'City', 'cities', $id, $oldData, null);
        session()->flash('success', 'City deleted successfully');  
        return redirect('cities');
    }
}
