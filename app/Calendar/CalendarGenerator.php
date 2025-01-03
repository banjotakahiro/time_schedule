<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarGenerator
{
    protected $currentDate;

public function __construct($date = null)
{
    // タイムゾーンを設定したCarbonインスタンスを作成
    $this->currentDate = $date 
        ? Carbon::parse($date['start'], 'Asia/Tokyo') // タイムゾーンを指定してパース
        : Carbon::now('Asia/Tokyo'); // 現在日時をAsia/Tokyoタイムゾーンで取得
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
