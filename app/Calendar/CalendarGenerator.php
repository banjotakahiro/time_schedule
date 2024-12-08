<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarGenerator
{
    protected $currentDate;

    public function __construct($date = null)
    {
        // 基準日を受け取らない場合は現在日時を設定
        $this->currentDate = $date ? Carbon::parse($date['start']) : Carbon::now();
    }

    public function getCurrentWeek()
    {
        return [
            'start' => $this->currentDate->copy()->startOfWeek(),
            'end' => $this->currentDate->copy()->endOfWeek(),
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

    public function getCurrentMonth()
    {
        return [
            'start' => $this->currentDate->copy()->startOfMonth(),
            'end' => $this->currentDate->copy()->endOfMonth(),
        ];
    }

    public function nextMonth()
    {
        $this->currentDate->addMonth();
        return $this->getCurrentMonth();
    }

    public function previousMonth()
    {
        $this->currentDate->subMonth();
        return $this->getCurrentMonth();
    }

    public function setCurrentDate($date)
    {
        $this->currentDate = Carbon::parse($date);
    }
}
