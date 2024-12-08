<x-app-layout>

  <!DOCTYPE html>
  <html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Schedule</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="bg-blue-50 min-h-screen p-5">
    <div class="container mx-auto bg-white shadow-lg rounded-lg p-6">
      <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">バイトシフト希望表</h1>
      <div class="flex justify-between items-center mb-4">
        <form action="{{ route('information_shifts.index') }}" method="GET">
          <input type="hidden" name="action" value="previousmonth">
          <input type="hidden" name="date" value="{{ json_encode($currentMonth) }}">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">前の月</button>
        </form>
        <h2 class="text-xl font-semibold">月: {{ $currentMonth['start']->format('Y年n月') }}</h2>
        <form action="{{ route('information_shifts.index') }}" method="GET">
          <input type="hidden" name="action" value="nextmonth">
          <input type="hidden" name="date" value="{{ json_encode($currentMonth) }}">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">次の月</button>
        </form>
      </div>
      <!-- カレンダー -->
      <table class="table-fixed w-full border-collapse">
        <thead>
          <tr class="bg-blue-200">
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">日</th>
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">月</th>
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">火</th>
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">水</th>
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">木</th>
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">金</th>
            <th class="w-1/7 border border-gray-300 px-4 py-2 text-gray-700 text-center">土</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($show_month_schedule['monthWeeks'] as $week)
          <tr>
            @foreach ($week as $date)
            @php
            // 現在の月を判定 (日付の月と範囲を直接比較)
            $currentMonthStart = $currentMonth['start']->format('Y-m');
            $dateMonth = \Carbon\Carbon::parse($date)->format('Y-m');
            $isCurrentMonth = $currentMonthStart === $dateMonth;
            @endphp
            <td class="h-24 border border-gray-300 text-left align-top bg-white hover:bg-blue-100 cursor-pointer relative">
              <!-- 日付部分 -->
              <div class="absolute top-1 right-2 font-bold text-sm 
                {{ $isCurrentMonth ? 'text-gray-800' : 'text-gray-400' }}">
                {{ \Carbon\Carbon::parse($date)->format('j') }}
              </div>
              <!-- イベント情報 -->
              @if ($isCurrentMonth)
              <div class="mt-6 text-sm text-gray-600"></div>
              <div class="text-xs text-gray-500"></div>
              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </body>

  </html>
</x-app-layout>
