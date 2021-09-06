<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CustomerController extends Controller
{
    
    // User Registration
    public function register(Request $request)
    {
        // validating email and password as per rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:64',
            'email' => 'required|string|email|max:64|unique:customers',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/',
        ]);

        // if validation failed, return failed status and corresponded errors 
        if ($validator->fails()) {

            $code = 400;
            $status = "failed";
            $message = "gagal dalam melakukan validasi email atau password";
            $error = $validator->errors();
            return response()->json(compact('code', 'status', 'message', 'error'), 400);
        }

        // if validation success, store the data to database
        $user = Customers::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')), //Hash::make is using bcrypt with a work factor of 10
        ]);

        //get token for this new user based on their credentials
        $token = JWTAuth::fromUser($user);
        $code = 201;
        $status = 'success';
        $message = 'proses registrasi berhasil';
        
        //return status, message, user info, and also token
        return response()->json(compact('code', 'status', 'message', 'user','token'),201);
    }
}