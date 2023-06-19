<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Booking extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'client_id',
        'reference_number',
        'vehicle_id',
        'services_id',
        'service_center_id',
        'contact_number',
        'status',
        'booking_date',
        'time',
        'notes',
        'updated_by',
    ];
}
