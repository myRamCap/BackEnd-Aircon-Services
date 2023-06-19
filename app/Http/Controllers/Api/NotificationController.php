<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return NotificationResource::collection(
            DB::select("SELECT a.*, b.first_name, b.last_name, c.name AS service_center FROM notifications a
                    LEFT JOIN users b ON a.corporate_id = b.id
                    LEFT JOIN service_centers c ON a.service_center_id = c.id
                    ORDER BY a.id DESC
            ")
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
    public function store(StoreNotificationRequest $request)
    {
        $data = $request->validated();
        $notification = Notification::create($data);
        return response(new NotificationResource($notification), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        $request->validated();

        $notification = Notification::find($request->id);
        $notification->corporate_id = $request->corporate_id;
        $notification->service_center_id = $request->service_center_id;
        $notification->datefrom = $request->datefrom;
        $notification->dateto = $request->dateto;
        $notification->title = $request->title;
        $notification->content = $request->content;
        $notification->image_url = $request->image_url;
        $notification->save();
        return response(new NotificationResource($notification), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
