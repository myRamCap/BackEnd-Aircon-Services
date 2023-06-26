<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
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
            'image' => 'nullable|required',
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

        $service_center = Vehicle::create($data);
        return response(new VehicleResource($service_center), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return VehicleResource::collection(
            // Vehicle::where('client_id', $id)->orderBy('id','desc')->get()
            Vehicle::join('clients', 'clients.id', '=', 'vehicles.client_id')
            ->select('vehicles.*', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
            ->where('vehicles.client_id', $id)->orderBy('aircon_name','ASC')->get()
         ); 
    }

}
