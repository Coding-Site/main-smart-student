<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        //
    }
    public function store(Request $request)
    {
        //
    }
    public function show(Request $request)
    {
        $user = Auth::guard('api')->user();
        return response()->json(['data' => $user], 200);
    }
    public function update(Request $request)
    {
        $admin = Auth::guard('api')->user();
        $validator = Validator::make($request->json()->all(), [
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users,phone,'. $admin->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        $admin->fill($request->only(['name', 'email', 'phone']));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/users', $image, $imageName);
            $admin->image = $imageName;
        }
        
        $admin->save();
        return response()->json(['message' => 'data updated successfully', 'data' => $admin], 200);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        $admin = Auth::guard('api')->user();
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json(['message' => 'password is incorrect'], 422);
        }
        $admin->password = Hash::make($request->password);
        $admin->save();
        return response()->json(['message' => 'password changed successfully'], 200);
    }
    public function destroy(string $id)
    {
        //
    }
}
