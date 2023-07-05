<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceCostRequest;
use App\Http\Requests\UpdateServiceCostRequest;
use App\Http\Resources\ServiceCostResource;
use App\Models\ServiceCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceCostController extends Controller
{
    public function index() {
         $service_cost = ServiceCost::join('service_centers', 'service_centers.id', '=', 'service_costs.service_center_id')
                            ->join('service_center_services', 'service_center_services.id', '=', 'service_costs.service_id')
                            ->join('services', 'services.id', '=', 'service_center_services.service_id')
                            ->join('users', 'users.id', '=', 'service_centers.corporate_manager_id')
                            ->select('service_costs.*', DB::raw("CONCAT(first_name, ' ', last_name) as fullname"), 'service_centers.name as service_center', 'services.name as service')
                            ->orderBy('service_costs.id','desc')
                            ->get();
        return response($service_cost, 201);
    }

    public function store(StoreServiceCostRequest $request) {
        $data = $request->validated();
        $data['price'] = $request->cost + $request->markup;
        $service_cost = ServiceCost::create($data);
        return response(new ServiceCostResource($service_cost), 201);
    }

    public function update(UpdateServiceCostRequest $request) {
        $data = $request->validated();
        $data['price'] = $request->cost + $request->markup;
        $service_cost = ServiceCost::create($data);
        return response(new ServiceCostResource($service_cost), 201);
    }
}
