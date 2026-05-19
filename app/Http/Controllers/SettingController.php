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

        $stateId = old('state_id', $setting->state_id ?? null);

        if ($stateId) {
            $cities = City::where('state_id', $stateId)
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
            'company_name' => 'required|string|min:3|max:100',
            'email' => 'required|string|max:128', 
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:1024',
            'qr_code' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:1024',
            'phone_number' => 'required|string|max:15|regex:/^[0-9+\-\s()]+$/',
            'toll_free_no' => 'nullable|string|max:500',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:1000',
            'zip_code' => 'required|string|max:6|min:6',
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
            'working_days' => 'nullable|string|max:100',
            'opening_time' => 'nullable|string|max:255',
            'closing_time' => 'nullable|string|max:255',
            'po_prefix' => 'nullable|string|max:10',
            'purchase_invoice_prefix' => 'nullable|string|max:10',
            'so_prefix' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'branch_location' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
        ];

        $messages = [
            '*.required' => 'This field is required.',
            '*.unique'   => 'This field already exists.',
            '*.regex' => 'This field is an invalid format',
            '*.min' => 'This field must be at least :min characters.',
            '*.max' => 'This field should not be more than :max characters.',
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
            'zip_code' => $request->zip_code,
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
            'so_prefix' => $request->so_prefix,
            'bank_name' => $request->bank_name,
            'branch_location' => $request->branch_location,
            'account_no' => $request->account_no,
            'ifsc_code' => $request->ifsc_code,
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

        if ($request->hasFile('qr_code')) {
            $qrCode = $request->file('qr_code');
            $qrCodeName = time() . '_' . $qrCode->getClientOriginalName();
            $uploadPath = public_path('uploads/qr_code');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $qrCode->move($uploadPath, $qrCodeName);
            if ($setting && $setting->qr_code) {
                $oldQrPath = public_path('uploads/qr_code/' . $setting->qr_code);
                if (file_exists($oldQrPath)) {
                    unlink($oldQrPath);
                }
            }
            $data['qr_code'] = $qrCodeName;
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
