<?php

namespace App\Http\Controllers\Api\tech;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Models\Verifytoken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function getDetails($id) {
        $tech = User::join('manage_users', 'manage_users.user_id', '=', 'users.id')
                ->join('service_centers' ,'service_centers.id', '=', 'manage_users.service_center_id')
                ->where('users.id', $id)
                ->selectRaw('first_name, last_name, CONCAT(service_centers.house_number, " ", service_centers.barangay) AS address, contact_number, email')
                ->first();
        return response([
            'data' => $tech
        ], 200);
    }

    public function verification(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|integer',
        ]);

        if ($validator->fails()){
            if ($validator->fails()){
                return response([
                    'errors' =>  $validator->errors()
               ], 422);
            }
        }

        $check_token = Verifytoken::where('token', $request->token)
                                ->where('email', $request->email)
                                ->where('is_activated', 0)
                                ->where('is_expired', 0)
                                ->first();

        $is_activated = User::where('email',$request->email)->where('is_activated', 1)->first();
        
        if ($check_token) {
            if ($is_activated) {
                $removeToken = Verifytoken::where('email', $request->email);
                $removeToken->delete();
                
                $user_ID = $is_activated['id'];
                $user = $is_activated['first_name']." ".$is_activated['last_name'];
                $token = $is_activated->createToken('main')->plainTextToken;
                return response(compact('user','token', 'user_ID')); 
            } else {
                // $check_token->is_activated = 1;
                // $check_token->save();
                $user = User::where('email', $check_token->email)->first();
                return response([
                    'data' =>  [
                            'user_id' => $user['id'],
                            'message' => 'New Account'
                        ]
               ], 200);
            }
        } else {
            return response([
                'message' => 'Invalid verification code'
            ], 422);
        }
    
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            if ($validator->fails()){
                return response([
                    'errors' =>  $validator->errors()
               ], 422);
            }
        }

        $user = User::where('email', '=', $request->email)->first();


        if ($user['role_id'] == 4) {
            if ($user['status'] === 'active') {
                $user_email = (new UserController)->email_send($request->email);
                return $user_email;
            } else {
                return response([
                    'errors' =>  ['user_status' => 'Im sorry, but your account has been deleted, and you can no longer use it.']
               ], 422);
            }
        } else {
            return response([
                'errors' =>  ['user_role' => 'Only Technician Account can login.']
           ], 422);
        }
    }

    public function changePass(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->symbols()
            ]
        ]);

        if ($validator->fails()){
            if ($validator->fails()){
                return response([
                    'errors' =>  $validator->errors()
               ], 422);
            }
        }

        if (isset($request->password)) {
            $data['password'] = bcrypt($request->password);
        }
        
        $get_user = User::where('id',$request->user_id)->first();
       
        $get_user->password = $data['password'];
        $get_user->is_activated = 1;
        $get_user->save();

        $user_ID = $get_user['id'];
        $user = $get_user['first_name']." ".$get_user['last_name'];
        $token = $get_user->createToken('main')->plainTextToken;

        $removeToken = Verifytoken::where('email', $get_user['email']);
        $removeToken->delete();

        return response(compact('user','token', 'user_ID')); 
    }

    public function email_send($email){
        $user = User::where('email', '=', $email)->first();

        $validToken = rand(100000, 999999);
        $get_token = new Verifytoken();
        $get_token->token = $validToken;
        $get_token->email = $user->email;
        $get_token->save();
        $get_user_email = $user->email;
        $get_user_name = $user->name;
        $mail = new WelcomeMail($get_user_email, $validToken, $get_user_name);
        $mail->from('admin@mangpogs.com');
        Mail::to($email)->send($mail);
        return response($user);
    }

    public function delete($id) {
        $user = User::where('id', '=', $id)->first();

        if ($user['role_id'] == 4 && $user['status'] === 'active') {
            $user = User::find($id);
            $user->status = 'inactive';
            $user->save();
            return response([
                'success' =>  ['user_status' => 'Successfully Deleted']
           ], 200);
        } else {
            return response([
                'errors' =>  ['user_status' => 'Youre not allowed to deleted this account']
           ], 422);
        }
    }

    public function logout(Request $request) {
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();

        return response('Logout Successfully', 204);
    }
}
