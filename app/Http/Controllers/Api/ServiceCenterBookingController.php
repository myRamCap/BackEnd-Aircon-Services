<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\ServiceCenterBookingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceCenterBookingController extends Controller
{
    /**
     * Display the upcoming booking of the client .
     */
    public function upcoming($id) {
        $query = DB::select("SELECT a.id, b.name as service_center, d.vehicle_name, c.name as services, a.booking_date, a.time  FROM bookings a
                    INNER JOIN service_centers b ON a.service_center_id = b.id
                    INNER JOIN services c ON a.services_id = c.id
                    INNER JOIN vehicles d ON a.vehicle_id = d.id
                    WHERE a.client_id = $id AND a.booking_date > CURRENT_DATE() OR (a.client_id = $id AND a.booking_date = CURRENT_DATE() AND a.time > CURRENT_TIME());
                ");

        return $query;
    }

    public function records($id) {
        $query = DB::select("SELECT a.id, b.name as service_center, d.vehicle_name, c.name as services, a.booking_date, a.time, a.status  FROM bookings a
                    INNER JOIN service_centers b ON a.service_center_id = b.id
                    INNER JOIN services c ON a.services_id = c.id
                    INNER JOIN vehicles d ON a.vehicle_id = d.id
                    WHERE a.client_id = $id AND a.booking_date < CURRENT_DATE() OR (a.client_id = $id AND a.booking_date = CURRENT_DATE() AND a.time < CURRENT_TIME());
                ");

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ServiceCenterBookingResource::collection(
            Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
                ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
                ->join('services', 'services.id', '=', 'bookings.services_id')
                ->join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                ->join('service_center_services', function ($join) {
                    $join->on('service_center_services.service_id', '=', 'services.id')
                         ->on('service_center_services.service_center_id', '=', 'service_centers.id');
                })
                ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
                ->orderBy('bookings.id','desc')
                ->get()
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
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'services_id' => 'required|integer',
            'service_center_id' => 'required|integer',
            'status' => 'nullable|string',
            'booking_date' => 'required|string',
            'time' => 'required|string',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()){
            if ($validator->fails()){
                return response([
                    'errors' =>  $validator->errors()
               ], 422);
            }
        }

        $data = [
            'client_id' => $request->client_id,
            'vehicle_id' => $request->vehicle_id,
            'services_id' => $request->services_id,
            'service_center_id' => $request->service_center_id,
            'status' => 'Up Coming',
            'booking_date' => $request->booking_date,
            'time' => $request->time,
            'notes' => $request->notes,
        ];


        // $booking = Booking::create($data);
        // $time_encode = DB::select("select count(a.time) as time 
        //         from time_slots a
        //         inner join (
        //             SELECT *
        //             FROM time_slots
        //             WHERE NOT EXISTS (
        //                 select a.time, addtime(a.time, b.estimated_time) as estimated_time from bookings a
        //                 inner join service_center_services b on a.services_id =  b.service_id
        //             WHERE (time_slots.time >= a.time  AND time_slots.time < addtime(a.time, b.estimated_time)  )
        //             and a.booking_date = '$request->booking_date'
        //             )
        //         ) b on a.time=b.time
        //         where a.time >= '$request->time' AND a.time < addtime('$request->time', '$request->estimated_time')
        //         AND a.service_center_id = '$request->service_center_id' "
        // );

        $time_encode = DB::select("SELECT count(time) as time
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
                    WHERE a.service_center_id = '$request->service_center_id' AND a.booking_date =  '$request->booking_date'
                )  b ON a.time >= b.time and a.time < b.estimated_time
                WHERE a.service_center_id = '$request->service_center_id'
                GROUP BY a.time , facility
            ) subquery
            WHERE  counter = facility AND (time_slots.time >= time  AND time_slots.time <=  time )  
        ) AND service_center_id = '$request->service_center_id'  
        AND time >= '$request->time' AND time < addtime('$request->time', '$request->estimated_time')
        ORDER BY time ASC"
        );

        // $time_check = DB::select(" select     from time_slots a
        // where a.time >= '$request->time'  AND a.time < addtime('$request->time', '$request->estimated_time')");

        $time_check = DB::select("SELECT count(time) as time
                FROM time_slots
                WHERE 
                (time >= '$request->time' AND time < addtime('$request->time', '$request->estimated_time')) 
                AND service_center_id = '$request->service_center_id'
        ");
 
        if ($time_encode == $time_check) {
            $booking = Booking::create($data);
            return response(new ServiceCenterBookingResource($booking), 201);
        } else {
            return response([
                'errors' => [ 'time' => ['The Time is not available for the Service. Please Select another time slot']]
           ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // return ServiceCenterBookingResource::collection(
        //     Booking::join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
        //             ->join('services', 'services.id', '=', 'bookings.services_id')
        //             ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
        //             ->join('service_center_services', 'service_center_services.service_id', '=', 'bookings.services_id')
        //             ->join('clients', 'clients.id', '=', 'bookings.client_id')
        //             ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
        //             ->where('bookings.service_center_id',$id)
        //             ->orderBy('bookings.id','desc')
        //             ->get()
        // );
         return ServiceCenterBookingResource::collection(
            Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
                ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
                ->join('services', 'services.id', '=', 'bookings.services_id')
                ->join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                ->join('service_center_services', 'service_center_services.service_id', '=', 'services.id')
                ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number')
                ->where('service_center_services.service_center_id',$id)
                ->orderBy('bookings.id','desc')
                ->get()
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $request->validated();

        $booking = Booking::find($request->id);
        $booking->client_id = $request->client_id;
        $booking->vehicle_id = $request->vehicle_id;
        $booking->services_id = $request->services_id;
        $booking->service_center_id = $request->service_center_id;
        $booking->status = $request->status;
        $booking->booking_date = $request->booking_date;
        $booking->time = $request->time;
        $booking->notes = $request->notes;
        $booking->save();
        return response(new ServiceCenterBookingResource($booking), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
