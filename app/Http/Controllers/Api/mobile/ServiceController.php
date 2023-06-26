<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
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
                ->get()
         ); 
    }

    public function login_default(Request $request){
         
    }
}
