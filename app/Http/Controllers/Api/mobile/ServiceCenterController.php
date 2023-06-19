<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCenterResource;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterOperationTime;
use App\Models\ServiceCenterService;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class ServiceCenterController extends Controller
{
    public function getdays($id) {
       $query = ServiceCenterOperationTime::select('service_center_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
                        ->where('service_center_id', '=', $id)
                        ->first();
        return $query;
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

        return response($serviceCenterData, 200);
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

        return response( $serviceCenterData, 200);
    }
}
