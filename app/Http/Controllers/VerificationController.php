<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Customers;
use App\Models\VerificationTokens;

class VerificationController extends Controller
{
    function __construct()
    {
        //set [Customers] as the model/role that will be used for this class
        \Config::set('auth.providers.users.model', \App\Models\Customers::class);
    }

    public function verifyToken(Request $request) {
        $token = $request->token;

        $tokenRecord = DB::table('verification_tokens')
                        ->where('token', $token)
                        ->first();
        
        if (!!$tokenRecord) {
            // If token exist in the database

            // Token is active if is_active = true AND current time is less than expiration time
            $isTokenActive = $tokenRecord->is_active && (Carbon::now()->lt($tokenRecord->expired_at));
            
            if ($isTokenActive) {
                // If token not expired/used yet
                $code = 200;
                $status = "success";
                $message = "verification success";

                return response()->json(compact('code', 'status', 'message'));
            } else {
                // If token expired or already used
                $code = 401;
                $status = "failed";
                $message = "verification failed, token is already expired or used.";

                return response()->json(compact('code', 'status', 'message'));
            }
            
        } else {
            
            // If token does not exist in the database
            $code = 404;
            $status = "failed";
            $message = "verification failed, invalid token.";

            return response()->json(compact('code', 'status', 'message'));
        }
    }
}
