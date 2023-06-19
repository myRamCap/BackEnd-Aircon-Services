<?php

namespace App\Http\Controllers\Api\OTP;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\Verifytoken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function verify(Request $request) {
        $get_token = $request->token;
        $get_email = $request->email;
        
        $get_token = Verifytoken::where('token',$get_token)->where('email',$get_email)->where('is_activated', 0)->where('is_expired', 0)->first();

        if ($get_token) {
            $get_token->is_activated = 1;
            $get_token->save();
            $user = User::where('email', $get_token->email)->first();
            return response($user);
        } else {
            return response([
                'message' => 'Invalid verification code'
            ], 422);
        }
        
    }

    public function verification(Request $request) {
        $get_token = $request->token;
        $get_email = $request->email;
        $get_token = Verifytoken::where('token',$get_token)->where('email',$get_email)->where('is_activated', 0)->where('is_expired', 0)->first();
        $is_activated = User::where('email',$get_email)->where('is_activated', 1)->first(); 
        
        if ($get_token) {
            if ($is_activated) {
                $removeToken = Verifytoken::where('email', $request->email);
                $removeToken->delete();
                
                $user_ID = $is_activated['id'];
                $role = $is_activated['role_id'];
                $user = $is_activated['first_name']." ".$is_activated['last_name'];
                $token = $is_activated->createToken('main')->plainTextToken;
                return response(compact('user','token','role', 'user_ID')); 
            } else {
                // $user = User::where('email',$get_email)->where('is_activated', 0)->first(); 
                // $user->is_activated = 1;
                // $user->save();
                $get_token->is_activated = 1;
                $get_token->save();
                $user = User::where('email', $get_token->email)->first();
                return response($user);
            }
        } else {
            return response([
                'message' => 'Invalid verification code'
            ], 422);
        }
    }

    public function expiredverification(Request $request) {
        $get_email = $request->email;

        $user_block = UserBlock::where('email', $get_email)->where('description', 'request OTP')->count();
 
        if($user_block != 0) {
            return response($user_block);
        }else {
            Verifytoken::where('email',$get_email)->where('is_expired', 0)->where('is_activated', 0)->update(['is_expired' => 1]);
            return response('OTP Expired');
        } 
    }

    public function resend(Request $request) {

        $email = $request->email;
        $attemp = Verifytoken::where('email', $email)->where('is_expired', 1)->count();
 
        if($attemp == 3) {
            $get_user_block = new UserBlock();
            $get_user_block->email = $email;
            $get_user_block->description = 'request OTP';
            $get_user_block->is_blocked = 1;
            $get_user_block->save();
            return response('blocked');
        } else {
            $validToken = rand(100000, 999999);
            $get_token = new Verifytoken();
            $get_token->token = $validToken;
            $get_token->email = $email;
            $get_token->save();
            $get_user_email = $email;
            $get_user_name = $email;
            Mail::to($email)->send(new WelcomeMail($get_user_email, $validToken, $get_user_name));

            return response('Email Resend successfully');
        }
    }
}
