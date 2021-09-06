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
    // User Login
    public function login(Request $request)
    {
        // retrieving data (email and password) from HTTP request
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                // if credentials not found, return error 400
                $status = "failed";
                $message = "login gagal, email dan password tidak cocok";

                return response()->json(compact('status', 'message'), 400);
            }
        } catch (JWTException $e) {
            // exception if attempting to verify token is not successful (internal server/unexpected error)
            $status = "failed";
            $message = "server tidak dapat memproses login dan membuat token";
            return response()->json(compact('status', 'message'), 500);
        }
        // return token in JSON format
        $status = "success";
        $message = "login berhasil";
        $data['token'] = $token;

        return response()->json(compact('status', 'message', 'data'), 200);
    }

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

            $status = "failed";
            $message = "gagal dalam melakukan validasi email atau password";
            $error = $validator->errors();
            return response()->json(compact('status', 'message', 'error'), 400);
        }

        // if validation success, store the data to database
        $user = Customers::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')), //Hash::make is using bcrypt with a work factor of 10
        ]);

        //get token for this new user based on their credentials
        $token = JWTAuth::fromUser($user);

        $status = 'success';
        $message = 'proses registrasi berhasil';
        
        //return status, message, user info, and also token
        return response()->json(compact( 'status', 'message', 'user','token'),201);
    }

    // User Info
    public function getUserInfo()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                // decode token and get info of user, if not found then return this
                return response()->json(['user_not_found'], 404);            
                
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            //exception thrown if token is expired (check config in jwt file)
            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            //exception thrown if token is invalid
            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            //exception thrown if token is absent/not exist
            return response()->json(['token_absent'], $e->getStatusCode());
        
        }
        
        //return detailed information of user in JSON
        return response()->json(compact('user'));
    }
}