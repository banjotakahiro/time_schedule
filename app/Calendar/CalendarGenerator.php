<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarGenerator
{
    protected $currentDate;

    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }

    public function getCurrentWeek()
    {
        return [
            'start' => $this->currentDate->startOfWeek(),
            'end' => $this->currentDate->endOfWeek(),
        ];
    }

    public function nextWeek()
    {
        $this->currentDate->addWeek();
        return $this->getCurrentWeek();
    }

    public function previousWeek()
    {
        $this->currentDate->subWeek();
        return $this->getCurrentWeek();
    }

    public function setCurrentDate($date)
    {
        $this->currentDate = Carbon::parse($date);
    }
}
