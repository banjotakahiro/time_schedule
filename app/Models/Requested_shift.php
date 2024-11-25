<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Requested_shift extends Model
{
     use HasFactory;

    public function start_diff()
    {
        return (new Carbon($this->start))->diffForHumans();
    }

    public function getStartDateAttribute()
    {
        return (new Carbon($this->start))->toDateString();
    }

    public function getStartTimeAttribute()
    {
        return date_parse_from_format('%Y-%m-%d %H:%i', $this->start)["hour"]
            ? (new Carbon($this->start))->toTimeString()
            : '';
    }

    public function getEndDateAttribute()
    {
        return (new Carbon($this->end))->toDateString();
    }

    public function getEndTimeAttribute()
    {
        return date_parse_from_format('%Y-%m-%m %H:%i', $this->end)['hour']
            ? (new Carbon($this->end))->toTimeString()
            : '';
    }
}