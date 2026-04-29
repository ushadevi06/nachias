<?php

namespace App\Http\Controllers;

use App\Models\Retailer;
use App\Models\Zone;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RetailersExport;

class RetailerController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view retailers')) {
            return unauthorizedRedirect();
        }
        
        if ($request->ajax()) {
            $retailers = Retailer::with(['zone', 'state', 'city', 'place'])->orderBy('id', 'desc')->get();
            $data = [];
            $count = 1;

            foreach ($retailers as $retailer) {
                $checked = $retailer->status === 'Active' ? 'checked' : '';
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox"
                        class="switch-input retailer-status-toggle"
                        data-id="' . $retailer->id . '" ' . $checked . '>
                    <span class="switch-toggle-slider"></span>
                </label>
                <div class="status_msg_' . $retailer->id . '"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view_details retailers')) {
                    $action .= '<a href="' . url('retailers/view/' . $retailer->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('edit retailers')) {
                    $action .= '<a href="' . url('retailers/add/' . $retailer->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if (auth()->id() == 1 || auth()->user()->can('delete retailers')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('retailers/delete/' . $retailer->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'name'     => $retailer->name . ' (' . ($retailer->code ?? '-') . ')',
                    'contact_info' => '
                        <div class="contact-info">
                            <div><i class="ri ri-mail-line icon-email"></i> ' . ($retailer->email ?? '-') . '</div>
                            <div><i class="ri ri-phone-line icon-phone"></i> ' . ($retailer->mobile_number ?? '-') . '</div>
                        </div>
                    ',
                    'location' => '
                        <div class="location-info">
                            <div><strong>State:</strong> ' . ($retailer->state->state_name ?? '-') . '</div>
                            <div><strong>City:</strong> ' . ($retailer->city->city_name ?? '-') . '</div>
                            <div><strong>Place:</strong> ' . ($retailer->place->place_name ?? '-') . '</div>
                            <div><strong>Zone:</strong> ' . ($retailer->zone->zone_name ?? '-') . '</div>
                        </div>
                    ',
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('retailers.view');
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details retailers')) {
            return unauthorizedRedirect();
        }
        $retailer = Retailer::with(['zone', 'state', 'city', 'place'])->findOrFail($id);
        return view('retailers.view_details', compact('retailer'));
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit retailers')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create retailers')) {
                return unauthorizedRedirect();
            }
        }
        
        $retailer = null;
        if ($id) {
            $retailer = Retailer::findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'name' => 'required|string|min:3|max:100',
                'code' => 'required|string|min:3|max:50|alpha_num|unique:retailers,code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_number' => 'nullable|numeric|digits_between:10,15|unique:retailers,mobile_number,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'email' => 'nullable|email|max:128|unique:retailers,email,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'zone_id' => 'nullable|exists:zones,id',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'place_id' => 'required|exists:places,id',
                'address_line_1' => 'nullable|string|min:3|max:150',
                'address_line_2' => 'nullable|string|min:3|max:150',
                'zipcode' => 'nullable|string|min:3|max:20',
            ];
            
            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.alpha_num' => 'This field should contain only letters and numbers.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
                '*.digits_between' => 'This field must be between :min and :max digits.',
                '*.numeric' => 'This field must be a number.',
            ];

            $validated = $request->validate($rules, $messages);

            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'zone_id' => $request->zone_id,
                'status' => $request->status,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'place_id' => $request->place_id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'zipcode' => $request->zipcode,
            ];

            if ($id) {
                $data['updated_by'] = auth()->id();
                $oldData = Retailer::find($id)->toArray();

                Retailer::where('id', $id)->update($data);
                $newData = Retailer::find($id)->toArray();
                addLog('update', 'Retailer', 'retailers', $id, $oldData, $newData);
                $message = 'Retailer updated successfully';

            } else {
                $data['created_by'] = auth()->id();
                $retailer = Retailer::create($data);
                $newData = $retailer->toArray();
                addLog('create', 'Retailer', 'retailers', $retailer->id, null, $newData);
                $message = 'Retailer added successfully';
            }

            return redirect('retailers')->with('success', $message);
        }

        $zones = [];
        $states = State::active()->get();
        $cities = [];
        $places = [];
        
        $stateId = old('state_id') ?? ($retailer->state_id ?? null);
        $cityId  = old('city_id')  ?? ($retailer->city_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
        }

        if ($cityId) {
            $places = Place::where('city_id', $cityId)->get();
            $zones = Zone::active()->whereRaw("FIND_IN_SET(?, city_ids)", [$cityId])->get();
        }
        
        return view('retailers.add', compact('retailer', 'zones', 'states', 'cities', 'places'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete retailers')) {
            return unauthorizedRedirect();
        }
        $retailer = Retailer::findOrFail($id);
        $oldData = $retailer->toArray();
        $retailer->delete();
        addLog('delete', 'Retailer', 'retailers', $id, $oldData, null);
        return redirect('retailers')->with('success', 'Retailer deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit retailers')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }
        $retailer = Retailer::findOrFail($id);
        $oldData = $retailer->toArray();
        $retailer->status = $request->status;
        $retailer->save();
        $newData = $retailer->toArray();
        addLog('update_status', 'Retailer Status', 'retailers', $id, $oldData, $newData);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view retailers')) {
            return unauthorizedRedirect();
        }
        return Excel::download(new RetailersExport, 'retailers_' . date('Ymd_His') . '.xlsx');
    }
}
