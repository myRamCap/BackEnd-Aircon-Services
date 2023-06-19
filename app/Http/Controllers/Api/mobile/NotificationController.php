<?php

namespace App\Http\Controllers\Api\mobile;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function notifications() {
        $currentDate = Carbon::today()->format('Y-m-d');

        $notifications = Notification::select('service_centers.name as service_center', 'service_centers.province', 'service_centers.municipality', 'notifications.datefrom', 'notifications.dateto', 'notifications.title', 'notifications.content', 'notifications.image_url')
            ->join('service_centers', function ($join) {
                $join->on(function ($query) {
                    $query->where('notifications.category', 'SELECTED')
                        ->whereRaw('JSON_CONTAINS(notifications.service_center, JSON_OBJECT("id", service_centers.ID), "$")');
                })
                ->orWhere(function ($query) {
                    $query->where('notifications.category', 'ALL')
                        ->whereColumn('notifications.corporate_account_id', 'service_centers.corporate_manager_id');
                });
            })
            ->where('notifications.dateto', '>=', $currentDate)
            ->get();

        return response([ 'data' => $notifications], 200);
    }
}
