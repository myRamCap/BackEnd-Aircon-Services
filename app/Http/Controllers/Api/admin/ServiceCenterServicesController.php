<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceCenterServicesRequest;
use App\Http\Requests\UpdateServiceCenterServicesRequest;
use App\Http\Resources\ServiceCenterServicesResource;
use App\Models\ServiceCenterService;
use Illuminate\Http\Request;

class ServiceCenterServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceCenterServicesResource::collection(
        //    ServiceCenterService::orderBy('id','desc')->get()
           ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
           ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
           ->select('service_center_services.*', 'services.name', 'services.details', 'services_logos.image_url')
        //    ->join('service_centers', 'service_centers.id', '=', 'service_center_services.service_center_id')
           ->orderBy('service_center_services.id','desc')
           ->get()
        ); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceCenterServicesRequest $request)
    {
        $data = $request->validated();
        $service = ServiceCenterService::create($data);
        return response(new ServiceCenterServicesResource($service), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return ServiceCenterServicesResource::collection(
            ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
                ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                ->select('service_center_services.*', 'services.name', 'services.details', 'services_logos.image_url')
                ->where('service_center_services.service_center_id', $id)
                ->orderBy('service_center_services.id','desc')
                ->get()
        ); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCenterServicesRequest $request, ServiceCenterService $serviceCenterServices)
    {
        $request->validated();

        $serviceCenterServices = ServiceCenterService::find($request->id);
        $serviceCenterServices->service_center_id = $request->service_center_id;
        $serviceCenterServices->service_id = $request->service_id; 
        $serviceCenterServices->estimated_time = $request->estimated_time; 
        $serviceCenterServices->estimated_time_desc = $request->estimated_time_desc; 
        $serviceCenterServices->save();
        return response(new ServiceCenterServicesResource($serviceCenterServices), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCenterService $serviceCenterServices)
    {
        //
    }
}
