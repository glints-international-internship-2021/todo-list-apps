<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Customers;
use App\Models\VerificationTokens;

use App\Mail\ForgotEmail;
class ForgotPasswordController extends Controller
{
    function __construct()
    {
        // set [Customers] as the model/role that will be used for this class
        \Config::set('auth.providers.users.model', \App\Models\Customers::class);
    }

    // To send forgot password link to customers email (with verification token)
    public function forgotPassword(Request $request) {
        // Retrive user info by their email
        $userModel = Customers::where('email', $request->email)
                            ->first();

        // If email does not exist
        if (!$userModel) {
            $code = 404;
            $status = "failed";
            $message = "Email does not exist in our database";
            return response()->json(compact('code', 'status', 'message'), $code);
        } 
        
        // If email exists
        $this->sendVerification($userModel['id'], $userModel['name'], $request->email);
        $code = 200;
        $status = "success";
        $message = "We have sent the reset password link to your email";
        return response()->json(compact('code', 'status', 'message'), $code);            
    }

    public function sendVerification($id, $user, $email) {
        //return response()->json(compact('user'));
        $token = $this->generateToken($id, $email);
        Mail::to($email)->send(new ForgotEmail($token, $user));
    }

    public function generateToken($id, $email) {
        // Generating token using base64 encoding
        $rawToken = $email . $id . Str::random(128);
        $token = base64_encode($rawToken);
        $this->saveToken($id, $email, $token);
        return $token;
    }

    public function saveToken($id, $email, $token) {
        DB::table('verification_tokens')->insert([
            'user_id' => $id,
            'token' => $token,
            'created_at' => Carbon::now(),
            'expired_at' => Carbon::now()->addMinutes(30), //token expires in 30 minutes after generated
            'is_active' => true
        ]);
    }

}
