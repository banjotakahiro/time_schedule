<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skill1()
    {
        return $this->belongsTo(Role::class, 'skill1');
    }

    public function skill2()
    {
        return $this->belongsTo(Role::class, 'skill2');
    }

    public function skill3()
    {
        return $this->belongsTo(Role::class, 'skill3');
    }
}
