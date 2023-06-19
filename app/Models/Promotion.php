<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Promotion extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'corporate_account_id',
        'category',
        'client',
        'datefrom',
        'dateto',
        'title',
        'content',
        'image_url',
    ];
}
 