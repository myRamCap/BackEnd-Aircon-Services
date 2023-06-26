<?php

namespace App\Http\Controllers\Api\tech;

use App\Http\Controllers\Controller;
use App\Models\ManageUser;
use App\Models\ServiceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCenterController extends Controller
{
    public function service_center($id) {
        $user = ManageUser::where('user_id', $id)->first();

        $service_center = ServiceCenter::where('id', $user['service_center_id'])
                            ->select('id', 'name', DB::raw("CONCAT(house_number, ' ', barangay, ' ', municipality, ', ', province) as address"), 'image')
                            ->first();

        return response([
            'data' => $service_center
        ], 200);

    }
}
