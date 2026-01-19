<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function authentication(Request $request) {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:8|max:15|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
        ],[
            'email.required' => 'This field is required.',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'This field is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one special character, and one symbol.',
        ]);
        if($validator->fails()){
            return redirect('/')->withErrors($validator)->withInput();
        }
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($remember) {
                Cookie::queue('email', $request->email, 43200);
                Cookie::queue('password', $request->password, 43200);
                Cookie::queue('remember', '1', 43200);
            } else {
                Cookie::queue(Cookie::forget('email'));
                Cookie::queue(Cookie::forget('password'));
                Cookie::queue(Cookie::forget('remember'));
            }
            return redirect()->intended('dashboard');
        }
        return redirect('/')->with('danger','Enter a valid credentials.')->withInput();
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->flash('success','Logout successfully');
        return redirect('/');
    }
    public function profile(Request $request){
        if(!$_POST){
            return view('profile'); 
        } else{
            $request->validate([
                'name' => 'required|min:3|max:50|regex:/^[a-zA-Z\s,:]+$/',
                'email' => 'required|email',
                'phone' => 'required|digits:10|not_in:0|unique:users,phone,'.$request->id,
                'password' => 'nullable|min:8|max:15|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ],[
                'name.required' => 'This field is required',
                'name.min' => 'Name must be at least 3 characters.',
                'name.max' => 'Name cannot be exceed 50 characters.',
                'name.regex' => 'This field is an invalid format.',
                'email.required' => 'This field is required',
                'email.regex' => 'Enter a valid email address (e.g., example@domain.com).', 
                'phone.required' => 'This field is required',
                'phone.regex' => 'Phone Number is an invalid format.',
                'phone.unique' => 'Phone Number already exists',
                'password.required' => 'This field is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.max' => 'Password cannot exceed 15 characters.',
                'password.regex' => 'This field is invalid format.',
                'profile_image.image' => 'File must be an image.',
                'profile_image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif.',
                'profile_image.max' => 'Image size cannot exceed 2MB.'
            ]);
            
            $user = Auth::user();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            } else {
                $user->password = $request->password_old;
            }
            
            if ($request->hasFile('profile_image')) {
                $uploadPath = public_path('uploads/profile/' . $user->id);
                if ($user->profile_image) {
                    $oldImagePath = $uploadPath . '/' . $user->profile_image;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $file = $request->file('profile_image');
                $filename = 'profile.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $user->profile_image = $filename;
            }
            
            $user->save();
            session()->flash('success','Profile updated successfully');
        }
       return redirect('profile');
    }
}
