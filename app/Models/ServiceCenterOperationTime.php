<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ServiceCenterOperationTime extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'category',
        'service_center_id',
        'opening_time',
        'closing_time',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];
}
