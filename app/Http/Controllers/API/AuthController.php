<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Models\Admin;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Delegate;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Rules\ClassroomIdExists;
use App\Rules\EducationLevelIdExists;

class AuthController extends AccessTokenController
{
    public function login(Request $request)
    {
        $credentials = $request->only(['phone', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        $user = Auth::user();
        $role = $user->userable;

        if ($role instanceof Admin) {
            $role = 'admin';
        }
        else if ($role instanceof Teacher) {
            $role = 'teacher';
        }
        else if (!$role instanceof Delegate) {
            $role = 'delegate';
        }
        else if (!$role instanceof Student) {
            $role = 'student';
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token = $user->createToken('MyApp')->accessToken;

        return response()->json(['token' => $token,'user'=>$user, 'role'=>$role]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'nulable|email|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'education_level_id' => ['required', 'integer', new EducationLevelIdExists],
            'classroom_id' => ['required', 'integer', new ClassroomIdExists]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->userable_type = 'App\Models\Student';
        $user->password = Hash::make($request->password);
        $user->save();

        $student = new Student();
        $student->user_id = $user->id;
        $student->education_level_id = $request->education_level_id;
        $student->classroom_id = $request->classroom_id;
        $student->save();

        $user->userable_id = $student->id;
        $user->save();

        $token = $user->createToken('MyApp')->accessToken;

        return response()->json(['token' => $token, 'user' => $user, 'role' => 'student'], 201);
    }
}