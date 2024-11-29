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
            @foreach ($show_schedule['weekDays'] as $number_count => $date)
            <th class="border border-blue-300 px-4 py-2 bg-blue-200 text-blue-900">{{ substr($date, 8, 2) }} {{ '(' . $show_schedule['dayOfWeek'][$number_count] .')' }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($show_schedule['userSchedules'] as $user)
          <tr>
            <td class="border border-blue-300 px-4 py-2 bg-blue-100 text-blue-900">{{ $user['name'] }}</td>
            @foreach ($show_schedule['weekDays'] as $date)
            <td class="border border-blue-300 bg-white hover:bg-blue-100 cursor-pointer">
              <button
                type="button"
                class="open-modal w-full h-full text-center flex items-center justify-center"
                data-date="{{ $date }}"
                data-user-id="{{ $user['user_id'] }}">
                @foreach ($user['schedule'][$date] as $requested_shift)
                @if($requested_shift == 'シフトなし')
                &nbsp;
                @else
                {{ $requested_shift }}
                @endif
                @endforeach
              </button>
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>



    <!-- モーダルのインクルード -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <!-- モーダルコンテンツ部分 -->
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8">
        <div id="modal-content">
            <!-- 動的に挿入されるコンテンツ -->
        </div>
      </div>
    </div>
    
  </body>

  </html>
  <script src="{{ asset('js/script.js') }}"></script>
</x-app-layout>
