<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Requested_shift extends Model
{
     use HasFactory;
        protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
    ];
        public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
