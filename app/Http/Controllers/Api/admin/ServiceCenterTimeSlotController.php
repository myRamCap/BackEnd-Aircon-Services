<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeSlotRequest;
use App\Http\Requests\UpdateTimeSlotRequest;
use App\Http\Resources\ServiceCenterTimSlotResource;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCenterTimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceCenterTimSlotResource::collection(
            TimeSlot::orderBy('id','desc')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimeSlotRequest $request)
    {
        $data = $request->validated();
        $timeslot = TimeSlot::create($data);
        return response(new ServiceCenterTimSlotResource($timeslot), 201);
    }

    public function timeslot($id, $year, $month, $day)
    {
        $date = $year.'/'.$month.'/'.$day;
        $carbonDate =  Carbon::parse($date);

        return ServiceCenterTimSlotResource::collection(
            DB::select("SELECT id, service_center_id, time, created_at
            FROM time_slots
            WHERE NOT EXISTS (
                SELECT time FROM (
                    SELECT count(a.time) as counter, a.time, facility
                    FROM time_slots a 
                    JOIN (
                        SELECT a.time, addtime(a.time, b.estimated_time) as estimated_time, a.service_center_id, c.facility
                        FROM bookings a 
                        INNER JOIN service_center_services b ON a.services_id = b.service_id AND a.service_center_id = b.service_center_id
                        INNER JOIN service_centers c ON a.service_center_id = c.id
                        WHERE a.service_center_id = $id AND a.booking_date =  '$date'
                    )  b ON a.time >= b.time and a.time < b.estimated_time
                    WHERE a.service_center_id = $id
                    GROUP BY a.time, facility
                ) subquery
                WHERE  counter = facility AND (time_slots.time >= time  AND time_slots.time <=  time )  
            ) AND service_center_id = $id  
            ORDER BY time ASC" )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return ServiceCenterTimSlotResource::collection(
            TimeSlot::where('service_center_id', $id)->orderBy('id','desc')->get()
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeSlot $timeSlot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeSlotRequest $request, TimeSlot $timeSlot)
    {
        $request->validated();

        $serviceCenterServices = TimeSlot::find($request->id);
        $serviceCenterServices->service_center_id = $request->service_center_id;
        $serviceCenterServices->time = $request->time; 
        // $serviceCenterServices->max_limit = $request->max_limit; 
        $serviceCenterServices->save();
        return response(new ServiceCenterTimSlotResource($serviceCenterServices), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeSlot $timeSlot)
    {
        //
    }
}
