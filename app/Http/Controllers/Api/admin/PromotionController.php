<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use App\Models\ManageUser;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PromotionResource::collection(
            Promotion::orderBy('id','desc')->get()
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
    public function store(StorePromotionRequest $request)
    {
        $data = $request->validated();
        $user = User::where('id', '=', $request->created_by)->first();

        $data = [
            'category' => $request->category,
            'client' => ($request->category == 'SELECTED') ? json_encode($request->client) : null,
            'datefrom' => $request->datefrom,
            'dateto' => $request->dateto,
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'created_by' => $request->created_by,
        ];

        if ($user['role_id'] == 2) {
            $data['corporate_account_id'] = $request->created_by;
        } else if ($user['role_id'] == 3) {
            $sc = ManageUser::where('user_id', '=', $request->created_by)->first();
            $data['corporate_account_id'] = $sc['corporate_manager_id'];
        }

        $promotion = Promotion::create($data);
        return response(new PromotionResource($promotion), 200);        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::where('id', '=', $id)->first();

        if ($user['role_id'] == 2) {
            // return NotificationResource::collection(
            //     DB::select("SELECT a.*, b.first_name, b.last_name, c.name AS service_center FROM notifications a
            //             LEFT JOIN users b ON a.corporate_id = b.id
            //             LEFT JOIN service_centers c ON a.service_center_id = c.id
            //             WHERE a.corporate_account_id = $id
            //             ORDER BY a.id DESC
            //     ")
            // );
      
            return PromotionResource::collection(
                // Promotion::where('corporate_account_id', '=', $id)
                //     ->orderBy('id','desc')
                //     ->get()
                DB::select("SELECT a.*, CONCAT(b.first_name, ' ', b.last_name) AS created_by, CONCAT(c.first_name, ' ', c.last_name) AS updated_by 
                FROM promotions a
                LEFT JOIN users b ON a.created_by = b.id
                LEFT JOIN users c ON a.updated_by = c.id
                WHERE a.corporate_account_id = $id
                ORDER BY a.id DESC")
            );
        } else if ($user['role_id'] == 3) {
            $sc = ManageUser::where('user_id', '=', $id)->first();
            $cs_id = $sc['service_center_id'];

            return PromotionResource::collection(
                DB::select("SELECT a.*, CONCAT(b.first_name, ' ', b.last_name) AS created_by, CONCAT(c.first_name, ' ', c.last_name) AS updated_by 
                FROM promotions a
                LEFT JOIN users b ON a.created_by = b.id
                LEFT JOIN users c ON a.updated_by = c.id
                JOIN service_centers d on a.corporate_account_id = d.corporate_manager_id
                WHERE d.id = $cs_id
                ORDER BY a.id DESC")
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $request->validated();

        $promotion = Promotion::find($request->id);
        $promotion->category = $request->category;
        // $promotion->client = $request->service_center_id;
        $promotion->client = ($request->category == 'SELECTED') ? json_encode($request->client) : null;
        $promotion->datefrom = $request->datefrom;
        $promotion->dateto = $request->dateto;
        $promotion->title = $request->title;
        $promotion->content = $request->content;
        $promotion->image_url = $request->image_url;
        $promotion->updated_by = $request->updated_by;
        $promotion->save();
        return response(new PromotionResource($promotion), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        //
    }
}
