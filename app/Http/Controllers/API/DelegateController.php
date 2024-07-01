<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delegate;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class DelegateController extends Controller
{
    public function index()
    {
        $delegates = Delegate::with('user')->get();
        return response()->json(['data' => $delegates], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'nullable|email|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try{
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'userable_type' => 'App\Models\Delegate',
            'password' => Hash::make($request->password),
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/delegates', $image, $imageName);
            $user->image = $imageName;
        }

        $delegate = Delegate::create([
            'user_id' => $user->id,
        ]);

        $user->userable_id = $delegate->id;
        $user->save();
        return response()->json(['data' => $user, 'message' => 'delegate added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating user'], 500);
        }
    }

    public function show($id)
    {
        try {
            $delegate = Delegate::with('user')->findOrFail($id);
            return response()->json(['data' => $delegate], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'delegate not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $delegate = Delegate::with('user')->findOrFail($id); 
        $user = $delegate->user;
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,'. $user->id,
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users,phone,'. $user->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $user->fill($request->only(['name', 'email','phone']));
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::delete('public/delegates/'. $user->image);
                }
                $image = $request->file('image');
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $user->image = $imageName;
                Storage::putFileAs('public/delegates', $image, $imageName);
            }
        
            $user->save();
            return response()->json(['data' => $user, 'message' =>'Delegate updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Delegate not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $delegate = Delegate::findOrFail($id);
            $user = User::findOrFail($delegate->user_id);
            $delegate->delete();
            $user->delete();
            return response()->json(['message' => 'delegate deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'delegate not found'], 404);
        }
    }
}
