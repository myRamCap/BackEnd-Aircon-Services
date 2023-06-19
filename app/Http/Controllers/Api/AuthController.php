<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Mail\WelcomeMail;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\Verifytoken;
use DateTime;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function email_send($email){
         /** @var User $user */
         $user = Auth::user();

         $validToken = rand(100000, 999999);
         $get_token = new Verifytoken();
         $get_token->token = $validToken;
         $get_token->email = $user->email;
         $get_token->save();
         $get_user_email = $user->email;
         $get_user_name = $user->name;
         Mail::to($user->email)->send(new WelcomeMail($get_user_email, $validToken, $get_user_name));
         return response($user);
    }

    public function forgot_pwd_send($email){
        // /** @var User $user */
        // $user = Auth::user();

        $user = User::where('email', $email)->first();

        $validToken = rand(100000, 999999);
        $get_token = new Verifytoken();
        $get_token->token = $validToken;
        $get_token->email = $user->email;
        $get_token->save();
        $get_user_email = $user->email;
        $get_user_name = $user->name;
        Mail::to($user->email)->send(new WelcomeMail($get_user_email, $validToken, $get_user_name));
        return response($user);
   }

    public function forgot_password(Request $request) {
        $email_check = User::where('email', $request->email)->first();

        if ($email_check) {
            $user_email = (new AuthController)->forgot_pwd_send($request->email);
            return $user_email;
        } else {
            return response([
                'errors' => [ 'email' => ['Email Address Not Found']]
           ], 422);
        }

    }

    public function login(LoginRequest $request) {
        $credentials = $request->validated();
        $login_attemp = LoginAttempt::where('email', $request->email)->count();
        $login_blocked = UserBlock::where('email', $request->email)->where('description', 'password failed')->first();
        $user_blocked = UserBlock::where('email', $request->email)->first();

        if (!Auth::attempt($credentials)){
            if($login_attemp >= 3) {
                if ($login_blocked) {
                    $created_at = new DateTime($login_blocked->created_at->format('Y-m-d H:i:s'));
                    $datetime_now = new DateTime(date('Y-m-d H:i:s'));
    
                    $dteDiff  = $created_at->diff($datetime_now);
                    $parts = explode(':',$dteDiff->format("%H:%I"));
                    $totalMinuts = 60 - ($parts[0]*60 + $parts[1]); 
                    return response([
                        'description' => $login_blocked->description,
                        'time' => $totalMinuts,
                        'blocked' => 'Your account has been blocked please wait'
                    ], 422);
                } else {
                    $get_user_block = new UserBlock();
                    $get_user_block->email = $credentials['email'];
                    $get_user_block->description = 'password failed';
                    $get_user_block->is_blocked = 1;
                    $get_user_block->save();
                }
                
            } else {
                $get_user_block = new LoginAttempt();
                $get_user_block->email = $credentials['email'];
                $get_user_block->description = 'password failed';
                $get_user_block->attempt = 1;
                $get_user_block->save();
                return response([
                    'message' => 'Provided email address or password is incorrect'
                ], 422);
            }
        }

        if ($user_blocked) {
            $created_at = new DateTime($user_blocked->created_at->format('Y-m-d H:i:s'));
            $datetime_now = new DateTime(date('Y-m-d H:i:s'));

            $dteDiff  = $created_at->diff($datetime_now);
            $parts = explode(':',$dteDiff->format("%H:%I"));
            $totalMinuts = 60 - ($parts[0]*60 + $parts[1]); 

            if ($totalMinuts > 0) {
                return response([
                    'description' => $user_blocked->description,
                    'time' => $totalMinuts,
                    'blocked' => 'Your account has been blocked please wait'
                ], 422);
            } else {
                $removeToken = UserBlock::where('email', $request->email);
                $removeToken->delete();
                $removeToken = LoginAttempt::where('email', $request->email);
                $removeToken->delete();
                $user_email = (new AuthController)->email_send($request->email);
                return $user_email;
            }
             
        } else {
            $removeToken = UserBlock::where('email', $request->email);
            $removeToken->delete();
            $removeToken = LoginAttempt::where('email', $request->email);
            $removeToken->delete();
            $user_email = (new AuthController)->email_send($request->email);
            return $user_email;
        }   
    }

    public function changePass(ChangePasswordRequest $request) {
        $data = $request->validated();
    
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $get_user = User::where('id',$request->id)->firstOrFail();
        $get_user->password = $data['password'];
        $get_user->is_activated = 1;
        $get_user->save();

        $user_ID = $get_user['id'];
        $role = $get_user['role_id'];
        $user = $get_user['first_name']." ".$get_user['last_name'];
        $token = $get_user->createToken('main')->plainTextToken;

        $removeToken = Verifytoken::where('email', $request->email);
        $removeToken->delete();

        return response(compact('user','token','role', 'user_ID')); 
    }

    public function logout(Request $request) {
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();

        return response('', 204);
    }
}
