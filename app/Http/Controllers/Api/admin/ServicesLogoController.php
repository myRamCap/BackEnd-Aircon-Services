<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceLogo;
use App\Http\Requests\UpdateServiceLogo;
use App\Http\Resources\ServicesLogoResource;
use App\Models\ServicesLogo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicesLogoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServicesLogoResource::collection(
            // ServicesLogo::join('users', 'users.id', '=', 'services_logos.created_by')
            // ->leftjoin('users', 'users.id', '=', 'services_logos.updated_by')
            // ->select('services_logos.*', 'users.first_name')
            // ->orderBy('id','desc')->get()
            DB::select("
            SELECT a.*, b.first_name AS created_by, c.first_name AS updated_by
            FROM services_logos a
            INNER JOIN users b ON a.created_by = b.id
            LEFT JOIN users c ON a.updated_by = c.id
            ORDER BY a.id DESC")
         );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceLogo $request)
    {
        $data = $request->validated();
        $serviceLogo = ServicesLogo::create($data);
        return response(new ServicesLogoResource($serviceLogo), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServicesLogo $serviceLogo)
    {
        return new ServicesLogoResource($serviceLogo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceLogo $request, ServicesLogo $serviceLogo)
    {
        $request->validated();

        $service_logo = ServicesLogo::find($request->id);
        $service_logo->title = $request->title;
        $service_logo->description = $request->description;
        $service_logo->image = $request->image;
        $service_logo->updated_by = $request->updated_by;
        $service_logo->image_url = $request->image_url;
        $service_logo->save();
        return response(new ServicesLogoResource($service_logo), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
