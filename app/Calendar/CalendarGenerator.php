<?php

namespace App\Calendar;

use Carbon\Carbon;

class CalendarGenerator
{
    protected $currentDate;

    public function __construct($date = null)
    {
        // 曜日設定を日本語にするために下2つを追加した。
        // 今回は$date[start]を指標にindex.phpで曜日を生成するのでendにはこの処理はいらない。
        setlocale(LC_TIME, 'ja_JP.UTF-8'); // ロケールを日本語に設定
        Carbon::setLocale('ja');          // Carbonでも日本語を適用
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

    public function setCurrentDate($date)
    {
        $this->currentDate = Carbon::parse($date);
    }

}
