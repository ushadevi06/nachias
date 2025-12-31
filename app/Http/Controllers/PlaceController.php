<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\State;
use App\Models\City;
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
                $statusSwitch = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input place-status-toggle"
                        data-id="' . $place->id . '"
                        ' . $checked . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $place->id . '"></div>
                ';
                

                $actionBtn = '<div class="button-box">';

                if (auth()->id() == 1 || auth()->user()->can('edit service-points')) {
                    $actionBtn .= '
                    <a href="' . url('places/add/' . $place->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete service-points')) {
                    $actionBtn .= '
                    <a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('places/delete/' . $place->id) . '\')">
                        <i class="icon-base ri ri-delete-bin-line"></i>
                    </a>';
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

        $states = State::active()->get();
        $cities = City::active()->get();

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
        $place = $id ? Place::findOrFail($id) : new Place();
        $oldData = $id ? $place->toArray() : null;

        if (request()->isMethod('post')) {
            $request = request();

            $validated = $request->validate([
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_name' => [
                    'required',
                    'max:255',
                    Rule::unique('places', 'place_name')->ignore($id)->whereNull('deleted_at')
                ],
                'place_type' => 'required|max:100',
                'latitude' => 'nullable|numeric|max_length[10]',
                'longitude' => 'nullable|numeric|max_length[11]',
                'status' => 'required|in:Active,Inactive'
            ], [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
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

        $states = State::active()->get();
        $cities = [];
        $stateId = old('state_id') ?? ($place->state_id ?? null);

        if ($stateId) {
            $cities = City::active()->where('state_id', $stateId)->get();
        }

        return view('places.add', compact('place', 'states', 'cities'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete service-points')) {
            return unauthorizedRedirect();
        }
        $place = Place::findOrFail($id);
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
