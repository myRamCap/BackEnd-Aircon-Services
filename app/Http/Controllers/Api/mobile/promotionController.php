<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class promotionController extends Controller
{
    public function promotions() {
        $currentDate = Carbon::today()->format('Y-m-d');

        $promotions = Promotion::select('clients.id', 'clients.first_name','clients.last_name', 'promotions.datefrom', 'promotions.dateto', 'promotions.title', 'promotions.content', 'promotions.image_url' )
            ->join('clients', function ($join) {
                $join->on(function ($query) {
                    $query->where('promotions.category', 'SELECTED')
                        ->whereRaw('JSON_CONTAINS(promotions.client, JSON_OBJECT("id", clients.ID), "$")');
                })
                ->orWhere(function ($query) {
                    $query->where('promotions.category', 'ALL');
                });
            })
            ->where('promotions.dateto', '>=', $currentDate)
            ->get();

        return response([ 'data' => $promotions], 200);
    }
}
