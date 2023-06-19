<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceCenterRequest;
use App\Http\Requests\UpdateServiceCenterRequest;
use App\Http\Resources\ServiceCenterResource;
use App\Models\ManageUser;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterService;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\UserRestriction;
use Illuminate\Http\Request;

class ServiceCenterController extends Controller
{
    public function service_center($id) {
        $user = User::where('id', $id)->first();
        
        if ($user['role_id'] == 1) {
            return ServiceCenterResource::collection(
                ServiceCenter::orderBy('id','desc')->get()
            ); 
        } else if ($user['role_id'] == 2) {
            $query = ServiceCenter::select('id', 'name')
                ->where('corporate_manager_id', $id)
                ->orderBy('id','desc')
                ->get();
            return response([ 'data' => $query], 200);
        } else if ($user['role_id'] == 3 || $user['role_id'] == 4) {
            $service_center = ManageUser::where('user_id', $id)->first();

                $query = ServiceCenter::select('id', 'name')
                    ->where('id', $service_center['service_center_id'])
                    ->orderBy('id','desc')
                    ->get();

                return response([ 'data' => $query], 200);
        }
    }

    public function corporate($id) {
        return ServiceCenterResource::collection(
            ServiceCenter::where('corporate_manager_id', $id)->get()
        ); 
    }

    public function getCategory($category) {
        $service_centers = ServiceCenter::where('category', $category)->get();
        $serviceCenterData = [];

        foreach ($service_centers as $service_center) {
            $services = ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
                        ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                        ->select('service_center_services.id', 'services.name', 'services.details', 'services_logos.image_url', 'service_center_services.estimated_time', 'service_center_services.estimated_time_desc' )
                        ->where('service_center_id', $service_center->id)
                        ->get();
            $timeslot = TimeSlot::where('service_center_id', $service_center->id)->get();
            
            $serviceCenterData[] = [
                'service_center' => [
                    'data' => array_merge($service_center->toArray(), [
                        'services' => $services,
                        'timeSlot' => $timeslot
                    ])
                ]
            ];
        }

        return response($serviceCenterData, 202);
    }

    public function getall() {
        $service_center = ServiceCenter::get();
        $serviceCenterData = [];
        foreach ($service_center as $service_center) {
            $services = ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
                        ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                        ->select('service_center_services.id', 'services.name', 'services.details', 'services_logos.image_url', 'service_center_services.estimated_time', 'service_center_services.estimated_time_desc' )
                        ->where('service_center_id', $service_center['id'])->get();
            $timeslot = TimeSlot::where('service_center_id', $service_center['id'])->get();
 
            $serviceCenterData[] = [
                'sevice_center' => $service_center,
                'services' => $services,
                'timeSlot' => $timeslot 
            ];
        }

        return response( $serviceCenterData, 202);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceCenterResource::collection(
            ServiceCenter::orderBy('id','desc')->get()
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
    public function store(StoreServiceCenterRequest $request)
    {
        $service_center = ServiceCenter::where('corporate_manager_id', $request->corporate_manager_id)->count();
        $restriction = UserRestriction::where('user_id', $request->corporate_manager_id)->first();

        if ($service_center == $restriction['allowed_sc']) {
            return response([
                'errors' => [ 'restriction' => ['Not allowed to create another Service Center']]
            ], 422);
        } else {
            $data = $request->validated();
            $municipality = $request->municipality;
            
            if (strpos($municipality, 'CITY OF ') !== false) {
                $index = strpos($municipality, 'CITY OF ') + strlen('CITY OF ');
                $shortCode = substr($municipality, $index, 3);
            } else {
                $shortCode = substr($municipality, 0, 3);
            }

            $service_center = ServiceCenter::create($data);
            $reference_number = $shortCode.'-'.$request->municipality_code;
            $service_center = ServiceCenter::find($service_center->id);
            $service_center->reference_number = $reference_number;
            $service_center->save();
            
            // $service_center = ServiceCenter::create($data);

            return response(new ServiceCenterResource($service_center), 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();
        
        if ($user['role_id'] == 1) {
            return ServiceCenterResource::collection(
                ServiceCenter::orderBy('id','desc')->get()
            ); 
        } else if ($user['role_id'] == 2) {
            return ServiceCenterResource::collection(
                ServiceCenter::where('corporate_manager_id', $id)->orderBy('id','desc')->get()
            ); 
        } else if ($user['role_id'] == 3 || $user['role_id'] == 4) {
            $service_center = ManageUser::where('user_id', $id)->first();

            return ServiceCenterResource::collection(
                ServiceCenter::where('id', $service_center['service_center_id'])->orderBy('id','desc')->get()
            ); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCenter $serviceCenter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCenterRequest $request, ServiceCenter $serviceCenter)
    {
        $request->validated();

        $municipality = $request->municipality;
            
        if (strpos($municipality, 'CITY OF ') !== false) {
            $index = strpos($municipality, 'CITY OF ') + strlen('CITY OF ');
            $shortCode = substr($municipality, $index, 3);
        } else {
            $shortCode = substr($municipality, 0, 3);
        }

        $service_logo = ServiceCenter::find($request->id);
        $service_logo->name = $request->name;
        $service_logo->category = $request->category;
        $service_logo->country = $request->country;
        $service_logo->house_number = $request->house_number;
        $service_logo->barangay = $request->barangay;
        $service_logo->municipality = $request->municipality;
        $service_logo->province = $request->province;
        $service_logo->longitude = $request->longitude;
        $service_logo->latitude = $request->latitude;
        $service_logo->facility = $request->facility;
        $service_logo->corporate_manager_id = $request->corporate_manager_id;
        $service_logo->reference_number = $shortCode.'-'.$request->municipality_code;
        $service_logo->image = $request->image;
        $service_logo->save();
        return response(new ServiceCenterResource($service_logo), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCenter $serviceCenter)
    {
        //
    }
}
