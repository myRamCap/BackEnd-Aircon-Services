<?php

namespace App\Http\Controllers\Api\tech;

use App\Http\Controllers\Controller;
use App\Mail\BookingCompleted;
use App\Mail\BookingInTransit;
use App\Mail\BookingMail;
use App\Models\Booking;
use App\Models\Client;
use App\Models\ManageUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{

    public function Completed_email_send($id){
        $booking_id = Booking::where('id', '=', $id)->latest()->first();
        $client = Client::where('id', '=', $booking_id['client_id'])->first();
      
        $user = Booking::join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                ->join('services', 'services.id', '=', 'bookings.services_id')
                ->select('bookings.reference_number', 'service_centers.name as service_center', 'services.name as services', 
                    DB::raw("CONCAT(service_centers.house_number, ' ', service_centers.barangay, ' ', service_centers.municipality, ', ', service_centers.province) AS address"),
                    DB::raw("CONCAT(DATE_FORMAT(bookings.booking_date, '%a, %M %e, %Y'), ' ', TIME_FORMAT(bookings.time, '%h:%i %p')) AS booking_datetime")
                )
                ->where('bookings.id', '=', $booking_id['id'])
                ->first();
        $email = $client['email'];
        $rn = $user['reference_number'];
        $sc = $user['service_center'];
        $services = $user['services'];
        $sc_address = $user['address'];
        $booking_date = $user['booking_datetime'];

        $mail = new BookingCompleted($email, $rn, $sc, $services, $sc_address, $booking_date);
        $mail->from('admin@mangpogs.com');
        Mail::to($email)->send($mail);

        return response($user, 200);
    } 

    public function Intransit_email_send($id){
        $booking_id = Booking::where('id', '=', $id)->latest()->first();
        $client = Client::where('id', '=', $booking_id['client_id'])->first();
      
        $user = Booking::join('service_centers', 'service_centers.id', '=', 'bookings.service_center_id')
                ->join('services', 'services.id', '=', 'bookings.services_id')
                ->select('bookings.reference_number', 'service_centers.name as service_center', 'services.name as services', 
                    DB::raw("CONCAT(service_centers.house_number, ' ', service_centers.barangay, ' ', service_centers.municipality, ', ', service_centers.province) AS address"),
                    DB::raw("CONCAT(DATE_FORMAT(bookings.booking_date, '%a, %M %e, %Y'), ' ', TIME_FORMAT(bookings.time, '%h:%i %p')) AS booking_datetime")
                )
                ->where('bookings.id', '=', $booking_id['id'])
                ->first();
        $email = $client['email'];
        $rn = $user['reference_number'];
        $sc = $user['service_center'];
        $services = $user['services'];
        $sc_address = $user['address'];
        $booking_date = $user['booking_datetime'];

        $mail = new BookingInTransit($email, $rn, $sc, $services, $sc_address, $booking_date);
        $mail->from('admin@mangpogs.com');
        Mail::to($email)->send($mail);

        return response($user, 200);
    } 

    public function available($id) {
        $sc = ManageUser::where('user_id', $id)->first();

        $available = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
        ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'longitude', 'latitude', 'booking_date', 'time')
        ->where('tech_id', null)
        ->where('bookings.service_center_id', $sc['service_center_id'])
        ->get();
        
        return response([
            'data' => $available
        ], 200);
    }

    public function details($id) {
        $booking = Booking::join('aircons as b', 'b.id', '=', 'bookings.aircon_id')
        ->join('clients as c', 'c.id', '=', 'bookings.client_id')
        ->join('service_center_services', 'service_center_services.id', 'bookings.services_id')
        ->join('service_costs', 'service_costs.services_id', 'service_center_services.id')
        ->select('bookings.id as booking_id', 'bookings.longitude', 'bookings.reference_number', 'bookings.booking_date', DB::raw("DATE_FORMAT(STR_TO_DATE(bookings.time, '%H:%i'), '%h:%i %p') as booking_time"), 'b.aircon_name', 'b.aircon_type', 'b.make', 'b.model', 'b.horse_power', 'b.serial_number', 'b.notes', 'c.first_name', 'c.last_name', 'c.address', 'c.contact_number', 'c.email', 'b.image')
        ->where('bookings.id', $id)
        ->get();
        
        return response([
            'data' => $booking
        ], 200);
    }

    public function completed($id) {
        $tech_sc = ManageUser::where('user_id', $id)->first();
        
        if ($tech_sc) {
            $sc_id = $tech_sc['service_center_id'];

            $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
            ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'longitude', 'latitude', 'booking_date', 'time')
            ->where('status', 'Completed')
            ->where('service_center_id', $sc_id)
            ->get();
            
            return response([
                'data' => $upcoming
            ], 200);
        } else {
            return response([
                'error' => 'no data response for this tech'
            ], 200);
        }
    }

    public function inprocess($id) {
        $tech_sc = ManageUser::where('user_id', $id)->first();
        
        if ($tech_sc) {
            $sc_id = $tech_sc['service_center_id'];

            $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
            ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'longitude', 'latitude', 'booking_date', 'time')
            ->where('status', 'In Process')
            ->where('service_center_id', $sc_id)
            ->get();
            
            return response([
                'data' => $upcoming
            ], 200);
        } else {
            return response([
                'error' => 'no data response for this tech'
            ], 200);
        }
    }

    public function update_inprocess($id) {
        $booking = Booking::where('id', $id)
                    ->where('status', 'In Process')
                    ->first();
        
        if ($booking) {
            (new BookingController)->Completed_email_send($id);
            $booking->status = 'Completed';
            $booking->save();
    
            return response([
                'message' => 'Booking is now completed'
            ], 200);
        } else {
            return response([
                'error' => 'Cant find the booking'
            ], 200);
        }
    }

    public function intransit($id) {
        $tech_sc = ManageUser::where('user_id', $id)->first();
        
        if ($tech_sc) {
            $sc_id = $tech_sc['service_center_id'];

            $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
            ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'longitude', 'latitude', 'booking_date', 'time')
            ->where('status', 'In Transit')
            ->where('service_center_id', $sc_id)
            ->get();
            
            return response([
                'data' => $upcoming
            ], 200);
        } else {
            return response([
                'error' => 'no data response for this tech'
            ], 200);
        }
    }

    public function update_intransit($id) {
        $booking = Booking::where('id', $id)
                    ->where('status', 'In Transit')
                    ->first();
        
        if ($booking) {
            $booking->status = 'In Process';
            $booking->save();
    
            return response([
                'message' => 'Status Update to In Process'
            ], 200);
        } else {
            return response([
                'error' => 'Cant find the booking'
            ], 200);
        }
    }

    public function upcoming($id) {
        $tech_sc = ManageUser::where('user_id', $id)->first();
        
        if ($tech_sc) {
            $sc_id = $tech_sc['service_center_id'];

            $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
            ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'longitude', 'latitude', 'booking_date', 'time')
            ->where('status', 'Up Coming')
            ->where('service_center_id', $sc_id)
            ->get();
            
            return response([
                'data' => $upcoming
            ], 200);
        } else {
            return response([
                'error' => 'no data response for this tech'
            ], 200);
        }
    }

    public function update_upcoming($id) {
        $currentDate = Carbon::now()->toDateString();
        $booking = Booking::where('id', $id)
                    ->where('status', 'Up Coming')
                    ->first();

        if ($booking) {
            if ($booking['booking_date'] == $currentDate) {
                (new BookingController)->Intransit_email_send($id);
                $booking->status = 'In Transit';
                $booking->save();
        
                return response([
                    'message' => 'Status Update to In transit'
                ], 200);
            } else {
                return response([
                    'error' => 'Not allowed to get this booking!'
                ], 200);
            }
            
        } else {
            return response([
                'error' => 'Cant find the booking'
            ], 200);
        }
    }
}
