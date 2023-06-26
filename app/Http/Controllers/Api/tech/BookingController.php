<?php

namespace App\Http\Controllers\Api\tech;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function details($id) {
        $booking = Booking::join('vehicles as b', 'b.id', '=', 'bookings.aircon_id')
        ->join('clients as c', 'c.id', '=', 'bookings.client_id')
        ->select('bookings.id as booking_id', 'bookings.reference_number', 'bookings.booking_date', DB::raw("DATE_FORMAT(STR_TO_DATE(bookings.time, '%H:%i'), '%h:%i %p') as booking_time"), 'b.aircon_name', 'b.aircon_type', 'b.make', 'b.model', 'b.horse_power', 'b.serial_number', 'b.notes', 'c.first_name', 'c.last_name', 'c.address', 'c.contact_number', 'c.email', 'b.image')
        ->where('bookings.id', $id)
        ->get();
        
        return response([
            'data' => $booking
        ], 200);
    }

    public function completed() {
        $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
        ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'booking_date', 'time')
        ->where('status', 'Completed')
        ->get();
        
        return response([
            'data' => $upcoming
        ], 200);
    }

    public function inprocess() {
        $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
        ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'booking_date', 'time')
        ->where('status', 'In Process')
        ->get();
        
        return response([
            'data' => $upcoming
        ], 200);
    }

    public function update_inprocess($id) {
        $booking = Booking::where('id', $id)
                    ->where('status', 'In Process')
                    ->first();
        
        if ($booking) {
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

    public function intransit() {
        $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
        ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'booking_date', 'time')
        ->where('status', 'In Transit')
        ->get();
        
        return response([
            'data' => $upcoming
        ], 200);
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

    public function upcoming() {
        $upcoming = Booking::join('clients', 'clients.id', '=', 'bookings.client_id')
        ->select('bookings.id as booking_id', 'reference_number', 'first_name', 'last_name', 'address', 'booking_date', 'time')
        ->where('status', 'Up Coming')
        ->get();
        
        return response([
            'data' => $upcoming
        ], 200);
    }

    public function update_upcoming($id) {
        $booking = Booking::where('id', $id)
                    ->where('status', 'Up Coming')
                    ->first();
        
        if ($booking) {
            $booking->status = 'In Transit';
            $booking->save();
    
            return response([
                'message' => 'Status Update to In transit'
            ], 200);
        } else {
            return response([
                'error' => 'Cant find the booking'
            ], 200);
        }
    }
}
