<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\ManageUser;
use App\Models\Service;
use App\Models\User;
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
    public function show($id)
    {
        $user = User::where('id', '=', $id)->first();

        if ($user['role_id'] == 2) {
            return ServiceResource::collection(
                Service::join('service_centers', 'service_centers.id' ,'=', 'services.service_center_id')
                ->where('service_centers.corporate_manager_id', '=', $id)
                ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                ->leftjoin('users AS created', 'created.id', '=', 'services.created_by')
                ->leftjoin('users AS updated', 'updated.id', '=', 'services.updated_by')
                ->select('services.*','services_logos.image_url', 'service_centers.name as service_center', 'created.first_name as c_fn', 'created.last_name as c_ln', 'updated.first_name as u_fn', 'updated.last_name as u_ln')
                ->orderBy('services.id','desc')
                ->get()
            );
        } else if ($user['role_id'] == 3) {
            $sc = ManageUser::where('user_id', '=', $id)->first();

            return ServiceResource::collection(
                Service::join('service_centers', 'service_centers.id' ,'=', 'services.service_center_id')
                ->where('service_centers.id', '=', $sc['service_center_id'])
                ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                ->leftjoin('users AS created', 'created.id', '=', 'services.created_by')
                ->leftjoin('users AS updated', 'updated.id', '=', 'services.updated_by')
                ->select('services.*','services_logos.image_url', 'service_centers.name as service_center', 'created.first_name as c_fn', 'created.last_name as c_ln', 'updated.first_name as u_fn', 'updated.last_name as u_ln')
                ->orderBy('services.id','desc')
                ->get()
            );
        }
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
        $service->updated_by = $request->updated_by;
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
