<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function authenticate(Request $request) {
        $data = $request->only('username', 'password');
        $validator = Validator::make($data, [
            'username' => ['required', 'string', 'min:1', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        try {
            if (!$token = JWTAuth::attempt($data)) {
                return response()->json([
                    'status' => 'Failed',
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                    'status' => 'Failed',
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
        return response()->json([
            'status' => 'Success',
            'message' => 'Login success.',
            'token' => $token,
        ]);
    }
}
