<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientLoginRequest;
use App\Http\Requests\ClientRegisterRequest;
use App\Http\Requests\ClientVerifyRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientsResource;
use App\Models\Client;
use App\Models\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function clients() {
        return ClientsResource::collection(
            Client::orderBy('first_name','asc')->get()
            // ServiceCenter::join('services_logos', 'services_logos.id', '=', 'services.id')->orderBy('services.id','desc')->get()
         ); 
    }

    public function index() {
        return ClientResource::collection(
            Client::orderBy('first_name','asc')->get()
            // ServiceCenter::join('services_logos', 'services_logos.id', '=', 'services.id')->orderBy('services.id','desc')->get()
         ); 
    }

    public function show($id) {
        return ClientResource::collection(
            Client::orderBy('id','desc')->where('id', $id)->get()
         ); 
    }

    public function otp_send($contact_number) {
        $validToken = rand(100000, 999999);
        $get_token = new ClientToken();
        $get_token->token = $validToken;
        $get_token->contact_number = $contact_number;
        $get_token->save();


        $ch = curl_init();
        $parameters = array(
            'apikey' => 'a2c6431ca76cd7ecc56a36afd837dd9f', //Your API KEY
            'number' => $contact_number,
            'message' => $validToken.' is you authentication code. for yur protection, do not share this code with anyone.',
        );
        curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);

        return response([
            'success' => true,
            'message' => 'OTP Sent Successfully'
        ], 200);

    }

    public function register(Request $request) {
        // $data = $request->validated();
        $validator = Validator::make($request->all(), [
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'required|email|unique:clients,email',
            'contact_number' => 'required|string|unique:clients,contact_number',
            'address' => 'string'
        ]);

         if ($validator->fails()){
            return response($validator->errors(), 422);
        }

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ];

        Client::create($data);

        $user_email = (new ClientController)->otp_send($request->contact_number);
        return $user_email;
    }

    public function verification(Request $request) {

        $validator = Validator::make($request->all(), [
            'contact_number' => 'required|string',
            'token' => 'required|string',
        ]);

         if ($validator->fails()){
            return response($validator->errors(), 422);
        }

        $check_token = ClientToken::where('token',$request->token)->where('contact_number',$request->contact_number)->where('is_activated', 0)->where('is_expired', 0)->first();
        $is_activated = Client::where('contact_number',$request->contact_number)->first(); 

        // return($check_token);

        if ($check_token) {
            $check_token->is_activated = 1;
            $check_token->save();
            
            $user_id = $is_activated['id'];
            $user = $is_activated['first_name']." ".$is_activated['last_name'];
            $data = $is_activated;
            $token = $is_activated->createToken('main')->plainTextToken;
            // $is_activated->remember_token = $token;
            // $is_activated->save();

            return response(compact('user','token', 'user_id', 'data')); 
        } else {
            return response([
                'message' => 'Invalid verification code'
            ], 422);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'contact_number' => 'required'
        ]);

         if ($validator->fails()){
            return response($validator->errors(), 422);
        }

        $client = Client::where('contact_number',$request->contact_number)->first();
        
        if (!$client){
            return response([
                 'contact_number' => ['Provided contact number is not registered']
            ], 422);
        }
        
        $user_email = (new ClientController)->otp_send($request->contact_number);
        return $user_email; 
    }
}
