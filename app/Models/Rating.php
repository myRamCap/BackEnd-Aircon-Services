<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Rating extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'client_id',
        'service_center_id',
        'booking_id',
        'tech_ref_id',
        'quality_of_service',
        'quick_service',
        'general_exp',
        'comments'
    ];
}
