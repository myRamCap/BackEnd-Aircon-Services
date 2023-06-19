<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vehicle extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'client_id',
        'vehicle_name',
        'chassis_number',
        // 'contact_number',
        'make',
        'model',
        'year',
        'image',
        'notes',
    ];
}
