<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use App\Rules\MaterialIdExists;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->get();
        return response()->json(['data' => $teachers], 200);
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
            'about' => 'nullable|string',
            'percentage' => 'nullable|numeric',
            'material_id' => ['required', 'integer', new MaterialIdExists]
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try{
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'userable_type' => 'App\Models\Teacher',
            'password' => Hash::make($request->password),
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/teachers', $image, $imageName);
            $user->image = $imageName;
        }

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'about' => $request->about,
            'percentage' => $request->percentage,
            'material_id' => $request->material_id,
        ]);

        $user->userable_id = $teacher->id;
        $user->save();
        return response()->json(['data' => [$teacher,$user], 'message' => 'teacher added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating user'], 500);
        }
    }

    public function show($id)
    {
        try {
            $teacher = Teacher::with('user')->findOrFail($id);
            return response()->json(['data' => $teacher], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'teacher not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        $user = $teacher->user;
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,'. $user->id,
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users,phone,'. $user->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'about' => 'nullable|string',
            'percentage' => 'nullable|numeric',
            'material_id' => ['required', 'integer', new MaterialIdExists],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $user->fill($request->only(['name', 'email','phone']));
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::delete('public/teachers/'. $user->image);
                }
                $image = $request->file('image');
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $user->image = $imageName;
                Storage::putFileAs('public/teachers', $image, $imageName);
            }
        
            $user->save();
            $teacher->update($request->only(['about', 'percentage','material_id']));
            return response()->json(['data' =>$teacher ,'message' => 'teacher updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'teacher not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $user = User::findOrFail($teacher->user_id);
            $teacher->delete();
            $user->delete();
            return response()->json(['success' => 'teacher deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'teacher not found'], 404);
        }
    }
}
