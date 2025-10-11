<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|digits:10|unique:users,phone',
            'business_name' => 'required|unique:users,business_name',
            'tin' => 'required|min:10|unique:users,tin',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],   
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'business_name' => $validatedData['business_name'],
            'tin' => $validatedData['tin'],
            'password' => Hash::make($validatedData['password'])
        ]);
        $token = $user->createToken('auth_token');
        return response()->json([
            'message' => 'Successfully created account',
            'token' => $token->accessToken
        ],201 );
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required|min:8'
        ]);

        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'message' => 'Invalid credentials'
            ], 404);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token');

        return response()->json([
            'message' => 'Login successfully',
            'token' => $token->accessToken
        ], 200);
        
    }
}
