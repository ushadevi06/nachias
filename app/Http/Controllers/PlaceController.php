<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\State;
use App\Models\City;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\ServiceProvider;
use App\Models\SalesAgent;
use App\Models\PurchaseCommissionAgent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view service-points')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {

            $query = Place::with(['state', 'city'])->orderBy('id', 'desc');

            if (!empty($request->state)) {
                $query->where('state_id', $request->state);
            }

            if (!empty($request->city)) {
                $query->where('city_id', $request->city);
            }

            $places = $query->get();
            $data = [];
            $count = 1;
            foreach ($places as $place) {

                $checked = $place->status === 'Active' ? 'checked' : '';
                $statusSwitch = '<label class="switch switch-success switch-lg"><input type="checkbox" class="switch-input place-status-toggle" data-id="' . $place->id . '"' . $checked . '><span class="switch-toggle-slider"><span class="switch-on"></span><span class="switch-off"></span></span></label><div class="status_msg_' . $place->id . '"></div>';

                $actionBtn = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit service-points')) {
                    $actionBtn .= '<a href="' . url('places/add/' . $place->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete service-points')) {
                    $actionBtn .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('places/delete/' . $place->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $actionBtn .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'state'      => $place->state->state_name ?? '-',
                    'city'       => $place->city->city_name ?? '-',
                    'place_name' => $place->place_name,
                    'place_type' => $place->place_type,
                    'status'     => $statusSwitch,
                    'action'     => $actionBtn
                ];
            }

            return response()->json([
                "data" => $data
            ]);
        }

        $states = State::active()->orderby('id','desc')->get();
        $cities = City::active()->orderby('id','desc')->get();

        return view('places.view', compact('states', 'cities'));
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit service-points')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create service-points')) {
                return unauthorizedRedirect();
            }
        }
        $place = $id ? Place::findOrFail($id) : null;
        $oldData = $id ? $place->toArray() : null;

        if (request()->isMethod('post')) {
            $request = request();

            $validated = $request->validate([
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_name' => [
                    'required',
                    'min:3',
                    'max:50',
                    Rule::unique('places', 'place_name')->ignore($id)->whereNull('deleted_at')
                ],
                'place_type' => 'required|max:50',
                'latitude' => 'nullable|numeric|max:10',
                'longitude' => 'nullable|numeric|max:11',
                'status' => 'required|in:Active,Inactive'
            ], [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ]);

            if ($id) {
                $validated['updated_by'] = auth()->id();
                $place->update($validated);
                $newData = Place::find($id)->toArray();
                addLog('update', 'Place', 'places', $place->id, $oldData, $newData);
                $message = 'Place updated successfully';
            } else {
                $validated['created_by'] = auth()->id();
                $newPlace = Place::create($validated);
                $newData = Place::find($newPlace->id)->toArray();
                addLog('create', 'Place', 'places', $newPlace->id, null, $newData);
                $message = 'Place added successfully';
            }
            return redirect('places')->with('success', $message);
        }

        $states = State::active()->orderBy('id','desc')->get();
        $cities = [];
        $stateId = old('state_id') ?? ($place->state_id ?? null);

        if ($stateId) {
            $cities = City::active()->where('state_id', $stateId)->orderBy('id','desc')->get();
        }

        return view('places.add', compact('place', 'states', 'cities'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete service-points')) {
            return unauthorizedRedirect();
        }
        $place = Place::findOrFail($id);
        $references = [
            [Supplier::class, 'place_id', 'Suppliers'],
            [Customer::class, 'place_id', 'Customers'],
            [ServiceProvider::class, 'place_id', 'Service Providers'],
            [SalesAgent::class, 'place_id', 'Sales Agents'],
            [PurchaseCommissionAgent::class, 'place_id', 'Purchase Commission Agents'],
        ];

        foreach ($references as [$model, $column, $label]) {
            if ($model::where($column, $id)->exists()) {
                session()->flash('danger', "This place is currently referenced in {$label} and cannot be deleted.");
                return redirect('places');
            }
        }
        $oldData = $place->toArray();
        $place->delete();
        addLog('delete', 'Place', 'places', $id, $oldData, null);
        return redirect('places')->with('success', 'Place deleted successfully');
    }

    public function updateStatus($id)
    {
        $place = Place::findOrFail($id);
        $oldData = $place->toArray();
        $place->status = request('status');
        $place->save();
        $newData = $place->toArray();
        addLog('update_status', 'Place Status', 'places', $place->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }
}
