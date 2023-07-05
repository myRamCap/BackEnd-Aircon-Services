<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TechInfo extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'service_center_id',
        'tech_id',
        'tech_ref_id'
    ];
}
