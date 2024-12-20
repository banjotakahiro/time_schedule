<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information_shift extends Model
{
    use HasFactory;
        protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'location',
        'color',
        'role1',
        'required_staff_role1',
        'role2',
        'required_staff_role2',
        'role3',
        'required_staff_role3',
    ];

        /**
     * role1 リレーション
     */
    public function role1Relation()
    {
        return $this->belongsTo(Role::class, 'role1');
    }

    /**
     * role2 リレーション
     */
    public function role2Relation()
    {
        return $this->belongsTo(Role::class, 'role2');
    }

    /**
     * role3 リレーション
     */
    public function role3Relation()
    {
        return $this->belongsTo(Role::class, 'role3');
    }

}
