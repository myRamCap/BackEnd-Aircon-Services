<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\ServiceCenterResource;
use App\Http\Resources\ServiceCenterServicesResource;
use App\Http\Resources\ServiceCenterTimSlotResource;
use App\Models\Booking;
use App\Models\ManageUser;
use App\Models\ServiceCenter;
use App\Models\ServiceCenterService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of center services.
     */
    public function service_center($id) {
        $user = User::where('id', $id)->first();

        if ($user['role_id'] == 2) {
            return ServiceCenterResource::collection(
                ServiceCenter::where('corporate_manager_id', $id)->get()
            ); 
        }else if ($user['role_id'] == 3 || $user['role_id'] == 4) {
            $sc = ManageUser::where('user_id', $id)->first();
            $sc_id = $sc['service_center_id'];

            return ServiceCenterResource::collection(
                ServiceCenter::where('id', $sc_id)->get()
            ); 
        }
    }

    /**
     * Display a listing of service center services.
     */
    public function services($id) {
        $user = User::where('id', $id)->first();

        if ($user['role_id'] == 2) {
            $sc = ServiceCenter::where('corporate_manager_id', $id)->first();
            $sc_id = $sc['id'];

            return ServiceCenterServicesResource::collection(
                ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
                    ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                    ->select('service_center_services.*', 'services.name', 'services.details', 'services_logos.image_url')
                    ->where('service_center_services.service_center_id', $sc_id)
                    ->orderBy('service_center_services.id','desc')
                    ->get()
            ); 
        } else if ($user['role_id'] == 3 || $user['role_id'] == 4) {
            $sc = ManageUser::where('user_id', $id)->first();
            $sc_id = $sc['service_center_id'];

            return ServiceCenterServicesResource::collection(
                ServiceCenterService::join('services', 'services.id', '=', 'service_center_services.service_id')
                    ->join('services_logos', 'services_logos.id', '=', 'services.image_id')
                    ->select('service_center_services.*', 'services.name', 'services.details', 'services_logos.image_url')
                    ->where('service_center_services.service_center_id', $sc_id)
                    ->orderBy('service_center_services.id','desc')
                    ->get()
            ); 
        }
    }

    /**
     * Display a listing of service center available timeslots.
     */
    public function timeslot($id, $year, $month, $day) {
        $date = $year.'/'.$month.'/'.$day;
        $carbonDate =  Carbon::parse($date);

        $user = User::where('id', $id)->first();

        if ($user['role_id'] == 2) {
            $sc = ServiceCenter::where('corporate_manager_id', $id)->first();
            $sc_id = $sc['id'];

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
                            WHERE a.service_center_id = $sc_id  AND a.booking_date =  '$date'
                        )  b ON a.time >= b.time and a.time <= b.estimated_time
                        WHERE a.service_center_id = $sc_id 
                        GROUP BY a.time, facility
                    ) subquery
                    WHERE  counter = facility AND (time_slots.time >= time  AND time_slots.time <=  time )  
                ) AND service_center_id = $sc_id  
                ORDER BY time ASC" )
            );
        } else if ($user['role_id'] == 3 || $user['role_id'] == 4) {
            $sc = ManageUser::where('user_id', $id)->first();
            $sc_id = $sc['service_center_id'];

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
                            WHERE a.service_center_id = $sc_id  AND a.booking_date =  '$date'
                        )  b ON a.time >= b.time and a.time <= b.estimated_time
                        WHERE a.service_center_id = $sc_id 
                        GROUP BY a.time, facility
                    ) subquery
                    WHERE  counter = facility AND (time_slots.time >= time  AND time_slots.time <=  time )  
                ) AND service_center_id = $sc_id  
                ORDER BY time ASC" )
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
 
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
            'status' => 'required|string',
            'booking_date' => 'required|string',
            'time' => 'required|string',
            'notes' => 'nullable',
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
            'status' => $request->status,
            'booking_date' => $request->booking_date,
            'time' => $request->time,
            'notes' => $request->notes,
        ];

        $time_encode = DB::select("SELECT count(time) as time
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
                    WHERE a.service_center_id = '$request->service_center_id' AND a.booking_date =  '$request->booking_date'
                )  b ON a.time >= b.time and a.time <= b.estimated_time
                WHERE a.service_center_id = '$request->service_center_id'
                GROUP BY a.time , facility
            ) subquery
            WHERE  counter = facility AND (time_slots.time >= time  AND time_slots.time <=  time )  
        ) AND service_center_id = $request->service_center_id 
        AND time >= '$request->time' AND time <= addtime('$request->time', '$request->estimated_time')
        ORDER BY time ASC"
        );

        $time_check = DB::select("SELECT count(time) as time
                FROM time_slots
                WHERE 
                (time >= '$request->time' AND time < addtime('$request->time', '$request->estimated_time')) 
                AND service_center_id = '$request->service_center_id'
        ");
 
        if ($time_encode == $time_check) {
            $sc = ServiceCenter::where('id', '=', $request->service_center_id)->first();
            $reference_number = $sc['reference_number'];

            $booking = Booking::create($data);
            $reference_number = $reference_number.'-00'.$booking->id;
            $booking = Booking::find($booking->id);
            $booking->reference_number = $reference_number;
            $booking->save();
            return response(new BookingResource($booking), 201);
        } else {
            return response([
                'errors' => [ 'time' => ['The Time is not available for the Service. Please Select another time or date']]
           ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();
        
        if ($user['role_id'] == 1) {
            return BookingResource::collection(
                Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
                    ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
                    ->join('services', 'services.id', '=', 'bookings.services_id')
                    ->join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                    ->join('service_center_services', function ($join) {
                        $join->on('service_center_services.service_id', '=', 'services.id')
                            ->on('service_center_services.service_center_id', '=', 'bookings.service_center_id');
                    })
                    ->leftjoin('users', 'users.id', '=', 'bookings.updated_by')
                    ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number', 'users.first_name as fn', 'users.last_name as ln')
                    ->orderBy('bookings.id','desc')
                    ->get()
            );
        } else if ($user['role_id'] == 2) {
            $query = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
            ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->join('services', 'services.id', '=', 'bookings.services_id')
            ->join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
            ->join('service_center_services', function ($join) {
                $join->on('service_center_services.service_id', '=', 'services.id')
                    ->on('service_center_services.service_center_id', '=', 'bookings.service_center_id');
            })
            ->leftjoin('users', 'users.id', '=', 'bookings.updated_by')
            ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number', 'users.first_name as fn', 'users.last_name as ln')
            ->where('service_centers.corporate_manager_id', $id)
            ->orderBy('bookings.id','desc')
            ->get();

            return BookingResource::collection(
                Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
                    ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
                    ->join('services', 'services.id', '=', 'bookings.services_id')
                    ->join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                    ->join('service_center_services', function ($join) {
                        $join->on('service_center_services.service_id', '=', 'services.id')
                            ->on('service_center_services.service_center_id', '=', 'bookings.service_center_id');
                    })
                    ->leftjoin('users', 'users.id', '=', 'bookings.updated_by')
                    ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number', 'users.first_name as fn', 'users.last_name as ln')
                    ->where('service_centers.corporate_manager_id', $id)
                    ->orderBy('bookings.id','desc')
                    ->get()
            );
        } else if ($user['role_id'] == 3 || $user['role_id'] == 4) {
            $sc_id = ManageUser::where('user_id', $id)->first();

            return BookingResource::collection(
                    Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
                    ->join('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')
                    ->join('services', 'services.id', '=', 'bookings.services_id')
                    ->join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                    ->join('service_center_services', function ($join) {
                        $join->on('service_center_services.service_id', '=', 'services.id')
                            ->on('service_center_services.service_center_id', '=', 'bookings.service_center_id');
                    })
                    ->leftjoin('users', 'users.id', '=', 'bookings.updated_by')
                    ->select('bookings.*', 'service_centers.name as service_center', 'services.name as service', 'vehicles.vehicle_name', 'service_center_services.estimated_time_desc', 'clients.first_name', 'clients.last_name', 'clients.contact_number', 'users.first_name as fn', 'users.last_name as ln')
                    ->where('service_centers.id', $sc_id['service_center_id'])
                    ->orderBy('bookings.id','desc')
                    ->get()
            );
        }
      
        
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
        $booking->updated_by = $request->updated_by;
        $booking->save();
        return response(new BookingResource($booking), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
