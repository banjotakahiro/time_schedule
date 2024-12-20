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
        <form action="{{ route('confirmed_shifts.index') }}" method="GET">
          <input type="hidden" name="action" value="previousmonth">
          <input type="hidden" name="date" value="{{ json_encode($currentMonth) }}">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">前の月</button>
        </form>
        <h2 class="text-xl font-semibold">月: {{ $currentMonth['start']->format('Y年n月') }}</h2>
        <form action="{{ route('confirmed_shifts.index') }}" method="GET">
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


            // 現在の月を判定
            $currentMonthStart = $currentMonth['start']->format('Y-m');
            $dateMonth = \Carbon\Carbon::parse($date)->format('Y-m');
            $isCurrentMonth = $currentMonthStart === $dateMonth;

            // 配列から該当日付のデータを取得
            $shiftsForDate = [];
            foreach ($confirmed_shifts as $item) {
            if ($item->date === $date) {
            $shiftsForDate[] = $item;
            }
            }

            @endphp


            <td
              class="h-24 border border-gray-300 text-left align-top hover:bg-blue-100 cursor-pointer relative"
              data-date="{{ $date }}">

              <!-- 日付を表示 -->
              <div class="absolute top-1 right-2 font-bold text-sm 
  {{ $isCurrentMonth ? 'text-gray-800' : 'text-gray-400' }}">
                {{ \Carbon\Carbon::parse($date)->format('j') }}
              </div>
              @if ($isCurrentMonth)
              <!-- シフト情報の表示 -->
              <div class="mt-6 text-sm text-gray-600">
                @if (!empty($shiftsForDate))
                @foreach ($shiftsForDate as $shift)
                <p>ユーザーID: {{ $shift->user_id ?? '未割り当て' }}</p>
                <p>時間: {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</p>
                <p>役割: {{ $shift->role ?? '未設定' }}</p>
                <p>状態: {{ $shift->status }}</p>
                <hr class="my-1 border-gray-300">
                @endforeach
                @else
                <p>シフトなし</p>
                @endif
              </div>
              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>

      </table>
    </div>
    <div class="absolute bottom-4 right-4">
      <form action="{{ route('confirmed_shifts.store') }}" method="POST" id="createShiftForm">
        @csrf
        <input type="hidden" name="date" value="{{ json_encode($currentMonth) }}">
        <!-- CREATE ボタン -->
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
          CREATE
        </button>
      </form>
    </div>
  </body>

  </html>
  <script src="{{ asset('js/confirmed_shift.js') }}"></script>
</x-app-layout>
