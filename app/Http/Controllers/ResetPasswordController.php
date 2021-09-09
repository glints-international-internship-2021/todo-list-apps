<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Customers;
use App\Models\VerificationTokens;

class ResetPasswordController extends Controller
{
    function __construct()
    {
        //set [Customers] as the model/role that will be used for this class
        \Config::set('auth.providers.users.model', \App\Models\Customers::class);
    }

    public function resetPassword(Request $request) {
        $token = $request->token;
        $oldPassword = $request->old_password;
        $newPassword = $request->new_password;

        //validator to check match 

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed|regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/',
            'old_password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {

            $code = 400;
            $status = "failed";
            $message = "failed to validate new password";
            $error = $validator->errors();
            return response()->json(compact('code', 'status', 'message', 'error'), 400);
        }


        $userModel = DB::table('customers') // Get hashed password from database
                        ->join('verification_tokens', 'customers.id', '=', 'verification_tokens.user_id')
                        ->where('verification_tokens.token', $token)
                        ->first();
                        

        if (!!$userModel) {
            // If token exist in the database (using userModel because verification_tokens table has been joined with customers table)

            $isTokenActive = $userModel->is_active && (Carbon::now()->lt($userModel->expired_at));

            
        // If token not expired and not used yet
            if ($isTokenActive) {

                $isOldPasswordMatched = Hash::check($oldPassword, $userModel->password);

                // If old password is matched with the hashed password in the database
                if ($isOldPasswordMatched) {

                    // Get hashed password from database
                    DB::table('customers') 
                        ->join('verification_tokens', 'customers.id', '=', 'verification_tokens.user_id')
                        ->where('verification_tokens.token', $token)
                        ->update([
                            'customers.password' => Hash::make($newPassword),
                            'verification_tokens.is_active' => false
                    ]);

                    $code = 200;
                    $status = "success";
                    $message = "password has been reset";

                    return response()->json(compact('code', 'status', 'message'));

                } else {
                    // Otherwise, return old password is invalid
                    $userModel->password = $newPassword; 
                    $code = 401;
                    $status = "failed";
                    $message = "old password does not match with our database";

                    return response()->json(compact('code', 'status', 'message'));
                }
            } else {

                // If token is already used
                if (!$userModel->is_active) {
                    $code = 401;
                    $status = "failed";
                    $message = "reset password failed, token is already used.";

                    return response()->json(compact('code', 'status', 'message'));
                } 

                // Otherwise return token is expired
                $code = 401;
                $status = "failed";
                $message = "reset password failed, token is already expired.";

                return response()->json(compact('code', 'status', 'message'));
            }
            
        } else {
            
            // If token does not exist in the database
            $code = 404;
            $status = "failed";
            $message = "reset password failed, invalid token.";

            return response()->json(compact('code', 'status', 'message'));
        }
    }
}
