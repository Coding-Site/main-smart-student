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

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('user')->all();
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
            'material_id' => $request->material_id,
        ]);

        $user->userable_id = $teacher->id;
        $user->save();
        return response()->json(['data' => $teacher, 'message' => 'teacher added successfully'], 200);
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
        $teacher = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'nulable|string',
            'email' => 'nulable|email|unique:users,email,'. $teacher->id,
            'phone' => 'nulable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users,phone,'. $teacher->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'about' => 'nullable|string',
            'percentage' => 'nullable|numeric',
            'material_id' => ['required', 'integer', new MaterialIdExists],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $teacher->fill($request->only(['name', 'email','phone']));
            if ($request->hasFile('image')) {
                if ($teacher->image) {
                    Storage::delete('public/teachers/'. $teacher->image);
                }
                $image = $request->file('image');
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $teacher->image = $imageName;
                Storage::putFileAs('public/teachers', $image, $imageName);
            }
        
            $teacher->save();
            $teacher = Teacher::findOrFail($id);
            $teacher->update($request->only(['about', 'percentage','material_id']));
            return response()->json(['success' => 'teacher updated successfully'], 200);
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
