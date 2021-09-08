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
                        ->where('token', base64_encode($token))
                        ->first();
        if (!!$tokenRecord) {
            // If token exist in the database
            $code = 200;
            $status = "success";
            $message = "verification success";
            return response()->json(compact('code', 'status', 'message'));
        } else {
            
            // If token does not exist in the database
            $code = 404;
            $status = "success";
            $message = "verification failed, invalid token.";
            return response()->json(compact('code', 'status', 'message'));
        }
    }
}
