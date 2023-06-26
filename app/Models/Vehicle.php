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
        'aircon_name',
        'aircon_type',
        'make',
        'model',
        'horse_power',
        'serial_number',
        'image',
        'notes',
    ];
}
