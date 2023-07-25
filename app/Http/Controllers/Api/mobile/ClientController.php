<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientsResource;
use App\Models\Client;
use App\Models\ClientTemp;
use App\Models\ClientToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function otp_cancel($number) {
        ClientTemp::where('contact_number', $number)->delete();

        return response([
            'message' => 'Register Cancel'
        ], 200);
    }

    public function edit_profile(Request $request) {
        return $request;

        $user = User::find($request->id);

        // if ($user) {
        //     // Update the user's information
        //     $user->email = 'New Name';
        //     $user->address = 'new.email@example.com';
        
        //     // Save the changes to the database
        //     $user->save();
        
        //     // Optionally, you can return a success message or redirect to a success page
        //     return redirect()->route('user.profile')->with('success', 'User updated successfully!');
        // } else {
        //     // Handle the case where the user with the given ID was not found
        //     return redirect()->route('user.profile')->with('error', 'User not found!');
        // }
    }

    // public function clients() {
    //     return ClientsResource::collection(
    //         Client::orderBy('first_name','asc')->get()
    //         // ServiceCenter::join('services_logos', 'services_logos.id', '=', 'services.id')->orderBy('services.id','desc')->get()
    //      ); 
    // }

    public function index() {
        return ClientResource::collection(
            Client::where('clients.active', '=', 1)
                ->orderBy('first_name','asc')
                ->get()
            // ServiceCenter::join('services_logos', 'services_logos.id', '=', 'services.id')->orderBy('services.id','desc')->get()
         ); 
    }

    public function show($id) {
        return ClientResource::collection(
            Client::where('clients.active', '=', 1)
                ->orderBy('id','desc')
                ->where('id', $id)->get()
         ); 
    }

    public function otp_send($contact_number) {
        if ($contact_number != '09123456789') {
            $validToken = rand(1000, 9999);
            $get_token = new ClientToken();
            $get_token->token = $validToken;
            $get_token->contact_number = $contact_number;
            $get_token->save();

            $ch = curl_init();
            $parameters = array(
                'apikey' => 'fb78b4c7aa9d8bc5d994a1b4b39f13a5', //Your API KEY
                'number' => $contact_number,
                'message' => $validToken.' is your authentication code for MangPogs. For your protection, do not share this code with anyone.',
                'sendername' => 'MangPogs'
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
    }

    public function register(Request $request) {
        // $data = $request->validated();
        $validator = Validator::make($request->all(), [
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'required|email',
            'contact_number' => 'required|string|unique:clients,contact_number',
            'address' => 'string',
            // 'longitude' => 'required|numeric|regex:/^\d{0,4}\.\d{1,15}$/',
            // 'latitude' => 'required|numeric|regex:/^\d{0,4}\.\d{1,15}$/',
        ]);

         if ($validator->fails()){
            return response($validator->errors(), 422);
        }

        try {
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
            ];
            // Code to save the registration data or perform any necessary actions
           
           ClientTemp::create($data);

            $user_email = (new ClientController)->otp_send($request->contact_number);
            return $user_email;
        } catch (\Exception $e) {
            // Log the error for further investigation 
            return response('Internal server error', 422);
        }
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
        $temp = ClientTemp::where('contact_number',$request->contact_number)->first(); 

        // return($check_token);

        if ($check_token) {
            if ($request->contact_number == '09123456789') {
                $user_id = $is_activated['id'];
                $user = $is_activated['first_name']." ".$is_activated['last_name'];
                $data = $is_activated;
                $token = $is_activated->createToken('main')->plainTextToken;
                // $is_activated->remember_token = $token;
                // $is_activated->save();

                return response(compact('user','token', 'user_id', 'data')); 

            } else if ($temp) {
                $data = [
                    'first_name' => $temp['first_name'],
                    'last_name' => $temp['last_name'],
                    'email' => $temp['email'],
                    'contact_number' => $temp['contact_number'],
                    'address' => $temp['address'],
                    'longitude' => $temp['longitude'],
                    'latitude' => $temp['latitude'],
                    'is_activated' => 1,
                ];

                $data = Client::create($data);

                // Remove Client Temp
                $remove_client_temp= ClientTemp::find($temp['id'])->delete();

                // Remove Client TOkens
                $remove_token = ClientToken::where('contact_number', $temp['contact_number'])->delete();

                $user_id = $data->id;
                $user = $temp['first_name']." ".$temp['last_name'];
                $data = $data;
                $token = $data->createToken('main')->plainTextToken;
                // $is_activated->remember_token = $token;
                // $is_activated->save();

                return response(compact('user','token', 'user_id', 'data')); 

            } else {
                $check_token->is_activated = 1;
                $check_token->save();
                
                $user_id = $is_activated['id'];
                $user = $is_activated['first_name']." ".$is_activated['last_name'];
                $data = $is_activated;
                $token = $is_activated->createToken('main')->plainTextToken;
                // $is_activated->remember_token = $token;
                // $is_activated->save();

                return response(compact('user','token', 'user_id', 'data')); 
            }
            
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


        

        if ($request->contact_number == '09123456789') {
            return response('Test Account', 200);

        } else {
             
            $client = Client::where('contact_number', $request->contact_number)->first();
 

            if (!$client){
                return response([
                     'contact_number' => ['Provided contact number is not registered']
                ], 422);
            }
    
            if ($client['active'] == 0) {
                return response([
                    'message' => ['This account is no more active. Kindly contact techsupport@mangpogs.com']
               ], 422);
            }
    
            $user_email = (new ClientController)->otp_send($request->contact_number);
            return $user_email; 
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string'
        ]);

        if ($validator->fails()){
            if ($validator->fails()){
                return response([
                    'errors' =>  $validator->errors()
               ], 422);
            }
        }

        $client = Client::find($request->id);

        if (!$client) {
            return response([
                'message' => ['User not found.']
           ], 422);
        }

        $client = Client::find($request->id);
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->address = $request->address;
        $client->save();
        return response(new ClientResource($client), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response([
                'message' => ['User not found.']
           ], 422);
        }


        $client->active = 0;
        $client->save();

        return response([
            'message' => ['User deleted successfully.']
       ], 200);
    
    }
}
