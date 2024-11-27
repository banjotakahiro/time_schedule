use Carbon\Carbon;
use App\Models\Requested_shift;
use App\Models\User;

public function showSchedule($currentWeek)
{
    // 現在の週の開始日と終了日
    $startOfWeek = $currentWeek['start']->copy();
    $dayOfWeek = ["月", "火", "水", "木", "金", "土", "日"];
    
    // 各日付を生成
    $weekDays = [];
    foreach (range(0, 6) as $day) {
    $weekDays[] = $startOfWeek->copy()->addDays($day);
    }
    
    // ユーザーとシフトを取得
    $users = User::with('requestedShifts')->get();
    
    // 各ユーザーに関連付けられたデータを整形
    $userSchedules = [];
    foreach ($users as $user) {
    $schedule = [];
    foreach ($weekDays as $date) {
    // 該当日のシフトをすべて取得
    $shifts = $user->requestedShifts->filter(function ($shift) use ($date) {
    return Carbon::parse($shift->start)->isSameDay($date);
    });
    
    // 日付をキー、シフトを配列で格納
    $schedule[$date->format('Y-m-d')] = $shifts->isNotEmpty()
    ? $shifts->pluck('title')->toArray() // シフトタイトルを取得
    : ['シフトなし']; // シフトがなければ 'シフトなし'
    }
    
    // スケジュールデータを格納
    $userSchedules[] = [
    'name' => $user->name,
    'schedule' => $schedule,
    ];
}
