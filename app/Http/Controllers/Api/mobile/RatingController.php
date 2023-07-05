<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileRatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function rating(Request $request) {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'service_center_id' => 'required|string',
            'booking_id' => 'required|string',
            'tech_ref_id' => 'required|string',
            'quality_of_service' => 'required|integer',
            'quick_service' => 'required|integer',
            'general_exp' => 'required|integer',
            'comments' => 'required|string'
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
            'service_center_id' => $request->service_center_id,
            'booking_id' => $request->booking_id,
            'tech_ref_id' => $request->tech_ref_id,
            'quality_of_service' => $request->quality_of_service,
            'quick_service' => $request->quick_service,
            'general_exp' => $request->general_exp,
            'comments' => $request->comments
        ];

        $service_center = Rating::create($data);
        return response(new MobileRatingResource($service_center), 200);
    }
}
