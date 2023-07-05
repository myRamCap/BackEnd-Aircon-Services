<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\ManageUser;
use App\Models\Rating;
use App\Models\ServiceCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function rating($id) {
        $user = User::where('id', $id)->first();

        if ($user['role_id'] == 1) {
            $rating = Rating::join('clients', 'clients.id', '=', 'ratings.client_id')
            ->select('ratings.*', DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) as client_name"), 'clients.contact_number')
            ->orderBy('ratings.id','desc')
            ->get();

            return response($rating, 200);
        } else if ($user['role_id'] == 2) {
            $service_center = ServiceCenter::where('corporate_manager_id', $id)->get();

            foreach ($service_center as $service_centers) {
                $rating = Rating::join('clients', 'clients.id', '=', 'ratings.client_id')
                    ->join('service_centers', 'service_centers.reference_number', '=', 'ratings.service_center_id')
                    ->select('ratings.*', DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) as client_name"), 'clients.contact_number')
                    ->where('service_centers.id', $service_centers->id)
                    ->orderBy('ratings.id','desc')
                    ->get();  
            }

            return response($rating, 200);
        } else if ($user['role_id'] == 3) {
            $sc = ManageUser::where('user_id', $id)->first();

            $rating = Rating::join('clients', 'clients.id', '=', 'ratings.client_id')
            ->join('service_centers', 'service_centers.reference_number', '=', 'ratings.service_center_id')
            ->select('ratings.*', DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) as client_name"), 'clients.contact_number')
            ->where('service_centers.id', $sc['service_center_id'])
            ->orderBy('ratings.id','desc')
            ->get();

            return response($rating, 200);
        }

        
    }
}
