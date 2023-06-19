<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'contact_number',
        'is_activated'
    ];
}
