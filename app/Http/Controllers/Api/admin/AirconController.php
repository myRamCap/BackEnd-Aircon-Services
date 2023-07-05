<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAirconRequest;
use App\Http\Resources\AirconResource;
use App\Models\Aircon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class airconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AirconResource::collection(
            Aircon::join('clients', 'clients.id', '=', 'aircons.client_id')
            ->select('aircons.*', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
            ->orderBy('aircons.id','desc')->get()
         ); 
    }

    public function aircon($id)
    {
        return AirconResource::collection(
            Aircon::join('clients', 'clients.id', '=', 'aircons.client_id')
            ->select('aircons.*', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
            ->where('aircons.client_id', $id)->orderBy('aircon_name','ASC')->get()
         ); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'aircon_name' => 'required|string',
            'aircon_type' => 'nullable|string',
            'make' => 'required|string',
            'model' => 'nullable|string',
            'horse_power' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'image' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()){
            if ($validator->fails()){
                return response([
                    'errors' =>  $validator->errors()
               ], 422);
            }
        }

        $data = [
            'client_id' => $request->client_id,
            'aircon_name' => $request->aircon_name,
            'aircon_type' => $request->aircon_type,
            'make' => $request->make,
            'model' => $request->model,
            'horse_power' => $request->horse_power,
            'serial_number' => $request->serial_number,
            'image' => $request->image,
            'notes' => $request->notes,
        ];

        $service_center = Aircon::create($data);
        return response(new AirconResource($service_center), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return AirconResource::collection(
            Aircon::join('clients', 'clients.id', '=', 'aircons.client_id')
            ->select('aircons.*', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
            ->where('aircons.client_id', $id)->orderBy('aircon_name','ASC')->get()
         ); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAirconRequest $request)
    {
        $request->validated();

        $aircon = Aircon::find($request->id);
        $aircon->client_id = $request->client_id;
        $aircon->aircon_name = $request->aircon_name;
        $aircon->aircon_type = $request->aircon_type;
        $aircon->make = $request->make;
        $aircon->model = $request->model;
        $aircon->horse_power = $request->horse_power;
        $aircon->serial_number = $request->serial_number;
        $aircon->image = $request->image;
        $aircon->notes = $request->notes;
        $aircon->save();
        return response(new AirconResource($aircon), 201);
    }
}
