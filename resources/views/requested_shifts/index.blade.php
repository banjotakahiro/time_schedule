<x-app-layout>

@php
  use Carbon\Carbon; // Carbonクラスをインポート
@endphp

  <!DOCTYPE html>
  <html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Schedule</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
  </head>

  <body class="bg-blue-50 min-h-screen p-5">
    <div class="container mx-auto bg-white shadow-lg rounded-lg p-6">
      <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">バイトシフト希望表</h1>

      <div class="flex justify-between mb-4">
        <form action="{{ route('requested_shifts.index') }}" method="GET">
          <input type="hidden" name="action" value="previousweek">
          <input type="hidden" name="date" value="{{ json_encode($currentWeek) }}">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">前の週</button>
        </form>
        <h2 class="text-xl">週: {{ $currentWeek['start']->format('Y年m月d日') }} 〜 {{ $currentWeek['end']->format('Y年m月d日') }}</h2>
        <form action="{{ route('requested_shifts.index') }}" method="GET">
          <input type="hidden" name="action" value="nextweek">
          <input type="hidden" name="date" value="{{ json_encode($currentWeek) }}">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">次の週</button>
        </form>
      </div>
      <table class="table-auto w-full border-collapse border border-blue-300">
        <thead>
          <tr>
            <th class="border border-blue-300 px-4 py-2 bg-blue-200 text-blue-900">名前</th>
            @php
              $startDate = $currentWeek['start']->copy(); // 開始日をコピーして変更を防ぐ
              $day_of_week = ["月","火","水","木","金","土","日"];
              $week_day = [];
            @endphp
            @foreach (range(0, 6) as $day) <!-- 0から6までの範囲をループ -->
              @php
                $date = $startDate->copy($day)->addDays($day);
                $week_day[] = $date;
              @endphp
              <th class="border border-blue-300 px-4 py-2 bg-blue-200 text-blue-900">{{ $date->format('d日') }} {{ '(' . $day_of_week[$day] .')' }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
              <tr>
                <td class="border border-blue-300 px-4 py-2 bg-blue-100 text-blue-900">{{ $user -> name }}</td>
                @if (!is_null($user->requestedShifts))
                    @foreach(range(0, 6) as $number)
                      @foreach ($user->requestedShifts as $requested_shift)
                          <td class="shift-cell border border-blue-300 px-4 py-2 bg-white hover:bg-blue-100 cursor-pointer">
                            @if (Carbon::parse($requested_shift->start)->isSameDay($week_day[$number]))
                              {{ $requested_shift -> title }}
                            @endif
                          </td>
                      @endforeach
                    @endforeach
                @else
                    <td class="shift-cell border border-blue-300 px-4 py-2 bg-white hover:bg-blue-100 cursor-pointer">シフトが見つかりません</td>
                @endif
              </tr>
            @endforeach

        </tbody>
      </table>
    </div>
    <script src="script.js"></script>
  </body>

  </html>
</x-app-layout>
