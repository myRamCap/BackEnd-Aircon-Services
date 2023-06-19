<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return ServiceResource::collection(
            // Service::orderBy('id','desc')->get()
            Service::join('services_logos', 'services_logos.id', '=', 'services.image_id')
                ->select('services.*','services_logos.image_url')
                ->orderBy('services.id','desc')
                ->get()
         ); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $service = Service::create($data);
        return response(new ServiceResource($service), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request)
    {
        $request->validated();

        $service = Service::find($request->id);
        $service->name = $request->name;
        $service->details = $request->details;
        $service->image_id = $request->image_id;
        $service->save();
        return response(new ServiceResource($service), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
