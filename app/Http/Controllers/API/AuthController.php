<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\Delegate;
use App\Models\Student;

class AuthController extends AccessTokenController
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

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
}