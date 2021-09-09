<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
        $userModel = DB::table('customers') // Get hashed password from database
                        ->join('verification_tokens', 'customers.id', '=', 'verification_tokens.user_id')
                        ->where('verification_tokens.token', $token)
                        ->first();

        $tokenRecord = DB::table('verification_tokens')
                        ->where('token', $token)
                        ->first();
                        

        if (!!$tokenRecord) {
            // If token exist in the database

            $isTokenActive = $tokenRecord->is_active && (Carbon::now()->lt($tokenRecord->expired_at));

            if ($isTokenActive) {
                // If token not expired/used yet

                $isOldPasswordMatched = Hash::check($oldPassword, $userModel->password);

                if ($isOldPasswordMatched) {
                    
                    DB::table('customers') // Get hashed password from database
                        ->join('verification_tokens', 'customers.id', '=', 'verification_tokens.user_id')
                        ->where('verification_tokens.token', $token)
                        ->update([
                            'customers.password' => $newPassword,
                            'verification_tokens.is_active' => false
                        ]);

                    $code = 200;
                    $status = "success";
                    $message = "password has been reset";


                    return response()->json(compact('code', 'status', 'message'));
                } else {
                    
                    $userModel->password = $newPassword; 
                    $code = 401;
                    $status = "failed";
                    $message = "old password does not match with our database";

                    return response()->json(compact('code', 'status', 'message'));
                }
            } else {
                
                // If token is already used
                if (!$tokenRecord->is_active) {
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
