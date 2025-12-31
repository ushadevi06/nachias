<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::with(['state', 'city'])->first();
        $states = State::where('status', 'Active')->get();
        $cities = [];

        if ($setting && $setting->state_id) {
            $cities = City::where('state_id', $setting->state_id)
                ->where('status', 'Active')
                ->get();
        }

        return view('settings', compact('setting', 'states', 'cities'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        $settingId = $setting ? $setting->id : null;

        $rules = [
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|max:500', 
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|min:1024|max:5120',
            'phone_number' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'toll_free_no' => 'nullable|string|max:500',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:1000',
            'cgst' => 'required|integer|min:0|max:100',
            'sgst' => 'required|integer|min:0|max:100',
            'igst' => 'required|integer|min:0|max:100',
            'pan_no' => [
                'nullable',
                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            ],
            'gst_no' => [
                'nullable',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            ],
            'cin_no' => [
                'nullable',
                'regex:/^[A-Z]{1}[0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/',
            ],
            'working_days' => 'nullable|string|max:255',
            'opening_time' => 'nullable|string|max:255',
            'closing_time' => 'nullable|string|max:255',
            'po_prefix' => 'nullable|string|max:10',
            'purchase_invoice_prefix' => 'nullable|string|max:10',
        ];

        $messages = [
            '*.required' => 'This field is required.',
            '*.unique'   => 'This field already exists.',
            '*.regex' => 'This field is an invalid format'
        ];

        $validated = $request->validate($rules, $messages);

        $data = [
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'toll_free_no' => $request->toll_free_no,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'cgst' => $request->cgst ?? 0,
            'sgst' => $request->sgst ?? 0,
            'igst' => $request->igst ?? 0,
            'pan_no' => $request->pan_no,
            'gst_no' => $request->gst_no,
            'cin_no' => $request->cin_no,
            'working_days' => $request->working_days,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'po_prefix' => $request->po_prefix,
            'purchase_invoice_prefix' => $request->purchase_invoice_prefix,
        ];

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $uploadPath = public_path('uploads/logo');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $logo->move($uploadPath, $logoName);
            if ($setting && $setting->logo) {
                $oldLogoPath = public_path('uploads/logo/' . $setting->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
            $data['logo'] = $logoName;
        }

        if ($setting) {
            $oldData = $setting->toArray();
            $setting->update($data);
            $newData = $setting->fresh()->toArray();
            addLog('update', 'Setting', 'settings', $setting->id, $oldData, $newData);
            $message = 'Settings updated successfully';
        } else {
            $setting = Setting::create($data);
            $newData = $setting->toArray();
            addLog('create', 'Setting', 'settings', $setting->id, null, $newData);
            $message = 'Settings created successfully';
        }
        return redirect('settings')->with('success', $message);
    }
}
