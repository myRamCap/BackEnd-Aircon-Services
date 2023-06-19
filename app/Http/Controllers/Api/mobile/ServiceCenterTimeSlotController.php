<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeSlotRequest;
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

        // $query = DB::select("SELECT id, service_center_id, time, max_limit, created_at  FROM time_slots
        // WHERE NOT EXISTS ( 
        //     SELECT a.time, addtime(a.time, b.estimated_time) as estimated_time 
        //     FROM bookings a INNER JOIN service_center_services b ON a.services_id = b.id
        //     WHERE time_slots.time >= a.time  AND time_slots.time < addtime(a.time, b.estimated_time) 
        //     AND a.service_center_id = $id AND a.booking_date =  '$date'
        // ) order by time ASC");

        // return($query);

        // return ServiceCenterTimSlotResource::collection(
        //     DB::select("SELECT id, service_center_id, time, max_limit, created_at  FROM time_slots
        //                 WHERE NOT EXISTS (
        //                     SELECT a.time, addtime(a.time, b.estimated_time) as estimated_time 
        //                     FROM bookings a INNER JOIN service_center_services b ON a.services_id = b.service_id
        //                     WHERE time_slots.time >= a.time  AND time_slots.time < addtime(a.time, b.estimated_time) 
        //                     AND a.service_center_id = $id AND a.booking_date =  '$date'
        //                 ) order by time ASC" )
        // );

        return ServiceCenterTimSlotResource::collection(
            DB::select("SELECT id, service_center_id, time, created_at
            FROM time_slots
            WHERE NOT EXISTS (
                SELECT time FROM (
                    SELECT count(a.time) as counter, a.time, facility
                    FROM time_slots a 
                    JOIN (
                        SELECT a.time, SUBTIME( addtime(a.time, b.estimated_time), '00:30:00') as estimated_time, a.service_center_id, c.facility
                        FROM bookings a 
                        INNER JOIN service_center_services b ON a.services_id = b.service_id AND a.service_center_id = b.service_center_id
                        INNER JOIN service_centers c ON a.service_center_id = c.id
                        WHERE a.service_center_id = $id AND a.booking_date =  '$date'
                    )  b ON a.time >= b.time and a.time <= b.estimated_time
                    WHERE a.service_center_id = $id
                    GROUP BY a.time, facility
                ) subquery
                WHERE  counter = facility AND (time_slots.time >= time  AND time_slots.time <=  time )  
            ) AND service_center_id = $id  
            ORDER BY time ASC" )
        );
 
    
    }

}
