<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\ManageUser;
use App\Models\Notification;
use App\Models\ServiceCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return NotificationResource::collection(
        //     DB::select("SELECT a.*, b.first_name, b.last_name, c.name AS service_center FROM notifications a
        //             LEFT JOIN users b ON a.corporate_id = b.id
        //             LEFT JOIN service_centers c ON a.service_center_id = c.id
        //             ORDER BY a.id DESC
        //     ")
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
    public function store(StoreNotificationRequest $request)
    {
        $data = $request->validated();
        $user = User::where('id', '=', $request->created_by)->first();
//  return $request->created_by ;
        $data = [
            'category' => $request->category,
            'service_center' => ($request->category == 'SELECTED') ? json_encode($request->service_center) : null,
            'datefrom' => $request->datefrom,
            'dateto' => $request->dateto,
            'title' => $request->title,
            'content' => $request->content,
            'created_by' => $request->created_by,
            'image_url' => $request->image_url,
        ];

        if ($user['role_id'] == 2) {
            $data['corporate_account_id'] = $request->created_by;
        } else if ($user['role_id'] == 3) {
            $sc = ManageUser::where('user_id', '=', $request->created_by)->first();
            $data['corporate_account_id'] = $sc['corporate_manager_id'];
        }
 
        $notification = Notification::create($data);
        return response(new NotificationResource($notification), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::where('id', '=', $id)->first();

        if ($user['role_id'] == 2) {
            return NotificationResource::collection(
                // Notification::leftjoin('users', 'users.id', '=', 'notifications.created_by')
                //         ->leftjoin('users', 'users.id', '=', 'notifications.updated_by')
                //         ->select('notifications.*', 'users')
                //         ->where('corporate_account_id', '=', $id)
                //         ->orderBy('id','desc')
                //         ->get()
                DB::select("SELECT a.*, CONCAT(b.first_name, ' ', b.last_name) AS created_by, CONCAT(c.first_name, ' ', c.last_name) AS updated_by 
                        FROM notifications a
                        LEFT JOIN users b ON a.created_by = b.id
                        LEFT JOIN users c ON a.updated_by = c.id
                        WHERE a.corporate_account_id = $id
                        ORDER BY a.id DESC
                ")
            );
        } else if ($user['role_id'] == 3) {
            $sc = ManageUser::where('user_id', '=', $id)->first();
            $cs_id = $sc['service_center_id'];

            return NotificationResource::collection(
                DB::select("SELECT a.*, CONCAT(b.first_name, ' ', b.last_name) AS created_by, CONCAT(c.first_name, ' ', c.last_name) AS updated_by 
                        FROM notifications a
                        LEFT JOIN users b ON a.created_by = b.id
                        LEFT JOIN users c ON a.updated_by = c.id
                        JOIN service_centers d on a.corporate_account_id = d.corporate_manager_id
                        WHERE d.id = $cs_id
                        ORDER BY a.id DESC
                ")
            );
        }
        
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
        $notification->category = $request->category;
        // $notification->corporate_id = $request->corporate_id;
        // $notification->service_center_id = $request->service_center_id;
        $notification->service_center = ($request->category == 'SELECTED') ? json_encode($request->service_center) : null;
        $notification->datefrom = $request->datefrom;
        $notification->dateto = $request->dateto;
        $notification->title = $request->title;
        $notification->content = $request->content;
        $notification->image_url = $request->image_url;
        $notification->updated_by = $request->updated_by;
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
