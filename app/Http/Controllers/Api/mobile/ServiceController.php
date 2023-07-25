<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                ->groupBy('services.id', 'services.name', 'services.description', 'services_logos.image_url')
                ->get()
         ); 
    }

    public function getServices($id) {
        $service_centers = ServiceCenter::get();

        $serviceCenterData = [];
        foreach ($service_centers as $service_center) {
            $services = ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
                        ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                        ->join('service_costs', 'service_center_services.id', '=', 'service_costs.service_id')
                        ->select('service_center_services.id', 'services.name', 'services.details', 'service_costs.price', 'service_center_services.estimated_time', 'service_center_services.estimated_time_desc' )
                        ->where('services.id', $id)
                        ->get();
         
            $serviceCenterData[] = [
                'service_center' => [
                    'data' => array_merge($service_center->toArray(), [
                        'services' => $services
                    ])
                ]
            ];
        }

        return response( $serviceCenterData, 200);
    }

    public function login_default(Request $request){
         
    }
}
