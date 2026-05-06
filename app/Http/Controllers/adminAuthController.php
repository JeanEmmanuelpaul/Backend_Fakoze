<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ CORRECTION ICI
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        // Authentification
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            $user = Auth::user(); // ✅ plus simple

            if ($user->role == 'admin') {

      $token = $request->user()->createToken('token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'token' => $token,
                    'id' => $user->id,
                    'name' => $user->name
                ], 200);

            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "You're not authorized to access admin panel."
                ], 401);
            }

        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Either email/password is incorrect'
            ], 401);
        }
    }
}
