<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ServiceCenter extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'category',
        'country',
        'house_number',
        'barangay',
        'municipality',
        'province',
        'longitude',
        'latitude',
        'facility',
        'corporate_manager_id',
        'image',
    ];
}
