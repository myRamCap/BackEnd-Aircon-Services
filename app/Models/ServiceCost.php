<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ServiceCost extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'service_center_id',
        'service_id',
        'cost',
        'markup',
        'price',
        'notes',
    ]; 
}
