<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmedShift extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'role_id',
        'date',
        'start_time',
        'end_time',
    ];
}
