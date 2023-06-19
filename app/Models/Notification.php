<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Notification extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'corporate_account_id',
        'category',
        // 'corporate_id',
        'service_center',
        'datefrom',
        'dateto',
        'title',
        'content',
        'image_url',
    ];
}
 