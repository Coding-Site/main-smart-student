<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use App\Rules\ClassroomIdExists;
use App\Rules\EducationLevelIdExists;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user')->get();
        return response()->json(['data' => $students], 200);
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
            'education_level_id' => ['required', 'integer', new EducationLevelIdExists],
            'classroom_id' => ['required', 'integer', new ClassroomIdExists]
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try{
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'userable_type' => 'App\Models\Student',
            'password' => Hash::make($request->password),
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            Storage::putFileAs('public/students', $image, $imageName);
            $user->image = $imageName;
        }

        $student = Student::create([
            'user_id' => $user->id,
            'education_level_id' => $request->education_level_id,
            'classroom_id' => $request->classroom_id,
        ]);

        $user->userable_id = $student->id;
        $user->save();
        return response()->json(['data' => $student, 'message' => 'student added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error creating user'], 500);
        }
    }

    public function show($id)
    {
        try {
            $student = Student::with('user')->findOrFail($id);
            return response()->json(['data' => $student], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'student not found'], 404);
        }
    }
    
    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $user = $student->user;
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,'. $user->id,
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users,phone,'. $user->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'education_level_id' => ['required', 'integer', new EducationLevelIdExists],
            'classroom_id' => ['required', 'integer', new ClassroomIdExists]
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }
        try {
            $user->fill($request->only(['name', 'email','phone']));
            if ($request->hasFile('image')) {
                if ($student->image) {
                    Storage::delete('public/students/'. $user->image);
                }
                $image = $request->file('image');
                $imageName = time(). '.'. $image->getClientOriginalExtension();
                $user->image = $imageName;
                Storage::putFileAs('public/students', $image, $imageName);
            }
        
            $user->save();
            $student->update($request->only(['education_level_id', 'classroom_id']));
            return response()->json(['data' => $student, 'message' => 'student updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'student not found'], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $user = User::findOrFail($student->user_id);
            $student->delete();
            $user->delete();
            return response()->json(['message' => 'student deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'student not found'], 404);
        }
    }
}
