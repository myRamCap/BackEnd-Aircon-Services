<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Http\Resources\PromotionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $data = [
            'category' => $request->category,
            'client' => ($request->category == 'SELECTED') ? json_encode($request->client) : null,
            'datefrom' => $request->datefrom,
            'dateto' => $request->dateto,
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
        ];

        $promotion = Promotion::create($data);
        return response(new PromotionResource($promotion), 200);        
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        //
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
        $promotion->client = $request->service_center_id;
        $promotion->client = ($request->category == 'SELECTED') ? json_encode($request->client) : null;
        $promotion->datefrom = $request->datefrom;
        $promotion->dateto = $request->dateto;
        $promotion->title = $request->title;
        $promotion->content = $request->content;
        $promotion->image_url = $request->image_url;
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
