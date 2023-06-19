<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCenterOperationTime;
use App\Http\Requests\StoreServiceCenterOperationTimeRequest;
use App\Http\Requests\UpdateServiceCenterOperationTimeRequest;
use App\Http\Resources\OperationTimeResource;
use App\Models\Time;
use App\Models\TimeSlot;

class OperationTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $municipality = 'CITY OF BATAC';
         // Extract the abbreviation
         $abbreviation = strtoupper(substr(str_replace(' ', '', $municipality), 0, 3));
    
    // Get the current year
    $year = date('Y');
    
    // Unique numerical identifier
    $identifier = '2468'; // Replace with your actual unique identifier generation logic
    
    // Concatenate the components to form the reference number
    $referenceNumber = $abbreviation . $year . '-' . $identifier;
    
    return $referenceNumber;
        // return OperationTimeResource::collection(
        //     ServiceCenterOperationTime::get()
        // );
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
    public function store(StoreServiceCenterOperationTimeRequest $request)
    {
        $data = $request->validated();

        $checking = ServiceCenterOperationTime::where('service_center_id','=', $request->service_center_id)->first();
        
        if ($checking) {
            return response([
                'errors' => [ 'restriction' => ['Not allowed to create another operation time']]
            ], 422);
        } else {
            if ($data['category'] === "24_hours") {
                $times = Time::get();
                foreach ($times as $time) {
                    TimeSlot::create([
                        'service_center_id' => $data['service_center_id'],
                        'time' => $time['time']
                    ]);
                }
            } else if ($data['category'] === "custom_time") {
                $open = $data['opening_time'];
                $close = $data['closing_time'];
                $times = Time::whereRaw("time >= '$open' and time <= '$close '")->get();

                foreach ($times as $time) {
                    TimeSlot::create([
                        'service_center_id' => $data['service_center_id'],
                        'time' => $time['time']
                    ]);  
                }     
            }
            $operation = ServiceCenterOperationTime::create($data);
            return response(new OperationTimeResource($operation), 200); 
        }
         
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return OperationTimeResource::collection(
            ServiceCenterOperationTime::where('service_center_id','=', $id)->get()
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCenterOperationTime $serviceCenterOperationTime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceCenterOperationTimeRequest $request, ServiceCenterOperationTime $serviceCenterOperationTime)
    {
        $data = $request->validated();

        TimeSlot::where('service_center_id', '=', $request->service_center_id)->delete();

        if ($data['category'] === "24_hours") {
            $times = Time::get();
            foreach ($times as $time) {
                TimeSlot::create([
                    'service_center_id' => $data['service_center_id'],
                    'time' => $time['time']
                ]);
            }
       } else if ($data['category'] === "custom_time") {
            $open = $data['opening_time'];
            $close = $data['closing_time'];
            $times = Time::whereRaw("time >= '$open' and time <= '$close '")->get();

            foreach ($times as $time) {
                TimeSlot::create([
                    'service_center_id' => $data['service_center_id'],
                    'time' => $time['time']
                ]);  
            }     
        }

        $promotion = ServiceCenterOperationTime::find($request->id);
        $promotion->category = $request->category;
        $promotion->opening_time = ($request->category == 'custom_time') ? $request->opening_time : null;
        $promotion->closing_time = ($request->category == 'custom_time') ? $request->closing_time : null;
        $promotion->monday = $request->monday;
        $promotion->tuesday = $request->tuesday;
        $promotion->wednesday = $request->wednesday;
        $promotion->thursday = $request->thursday;
        $promotion->friday = $request->friday;
        $promotion->saturday = $request->saturday;
        $promotion->sunday = $request->sunday;
        $promotion->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCenterOperationTime $serviceCenterOperationTime)
    {
        //
    }
}
