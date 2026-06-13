<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthenticationController extends Controller
{
 
 public function appear (Request $request){
   return    $data =  User::all();
       //  dd($data);
    }

public function register(Request $request)
{
    $field = $request->validate([
        'name' => 'required|string|max:250',
        'email' => 'required|email|max:100|unique:users',
        'password' => 'required|string|min:5|confirmed'
    ]);

    $field['password'] = Hash::make($field['password']);

    $user = User::create($field);

    $token = $user->createToken($user->name)->plainTextToken;

    return //response()->json
    ([
        'user' => $user,
        'token' => $token
    ]);
}
  public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            "message" => "The provided credentials are not correct"
        ]);
    }

    $token = $user->createToken($user->name)->plainTextToken;

    return response()->json([
        'data' => $user,
        'token' => $token,
        'message' => "login in passed successfully"
    ], 200);
}

public function logout(Request $request)
{
     $request->user()->currentAccessToken()->delete();
     // $request->user()->Tokens()->delete();
    return response()->json([
        'message' => 'Logged out successfully'
    ], 200);
  }
}


