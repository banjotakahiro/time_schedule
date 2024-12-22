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
                @php
                @endphp
                @if (!empty($shiftsForDate))
                @foreach ($shiftsForDate as $shift)
                <p>ユーザーID: {{ $shift->user_id ?? '未割り当て' }}</p>
                <p>時間: {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</p>
                <p>役割: {{ $shift->role_id ?? '未設定' }}</p>
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

    <div class="mt-8 mb-8">
      <h2 class="text-2xl font-bold text-blue-700 mb-4 text-center">ユーザーごとのシフト回数</h2>
      <table class="table-fixed w-full border-collapse bg-white shadow-md rounded-lg">
        <thead>
          <tr class="bg-blue-200">
            <th class="border border-gray-300 px-4 py-2 text-gray-700 text-center">ユーザーID</th>
            <th class="border border-gray-300 px-4 py-2 text-gray-700 text-center">シフト回数</th>
          </tr>
        </thead>
        <tbody>
          @php
          $shiftCounts = [];
          foreach ($confirmed_shifts as $shift) {
          $key = $shift->user_id ?? '未割り当て'; // user_idがnullの場合は'未割り当て'をキーにする
          if (!isset($shiftCounts[$key])) {
          $shiftCounts[$key] = 0;
          }
          $shiftCounts[$key]++;
          }
          // user_id順に並び替え。ただし、未割り当て('未割り当て')を最後に表示
          uksort($shiftCounts, function($a, $b) {
          if ($a === '未割り当て') return 1; // '未割り当て'を最後に
          if ($b === '未割り当て') return -1;
          return $a <=> $b; // 通常の昇順ソート
            });
            @endphp
            @foreach ($shiftCounts as $userId => $count)
            <tr>
              <td class="border border-gray-300 px-4 py-2 text-gray-700 text-center">{{ $userId }}</td>
              <td class="border border-gray-300 px-4 py-2 text-gray-700 text-center">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>


    <table id="shift_constraints-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-3">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="py-3 px-6">ステータス</th>
          <th scope="col" class="py-3 px-6">ユーザーID</th>
          <th scope="col" class="py-3 px-6">日付</th>
          <th scope="col" class="py-3 px-6">ペアリングユーザーID</th>
          <th scope="col" class="py-3 px-6">最大シフト回数</th>
          <th scope="col" class="py-3 px-6">追加情報</th>
          <th scope="col" class="py-3 px-6"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($shift_constraints as $shift_constraint)
        <tr id="shift-constraint-row-{{ $shift_constraint->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
          <td class="py-4 px-6 shift-constraint-status-display">{{ $shift_constraint->status }}</td>
          <td class="py-4 px-6 shift-constraint-user-id-display">{{ $shift_constraint->user_id }}</td>
          <td class="py-4 px-6 shift-constraint-date-display">{{ $shift_constraint->date ?? '-' }}</td>
          <td class="py-4 px-6 shift-constraint-paired-user-id-display">{{ $shift_constraint->paired_user_id ?? '-' }}</td>
          <td class="py-4 px-6 shift-constraint-max-shifts-display">{{ $shift_constraint->max_shifts ?? '-' }}</td>
          <td class="py-4 px-6 shift-constraint-extra-info-display">{{ $shift_constraint->extra_info ?? '-' }}</td>
          <td class="py-4 px-6">
            <button class="inline-block bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 shift-constraint-btn-edit"
              data-id="{{ $shift_constraint->id }}">
              {{ __('Edit') }}
            </button>
            <button class="inline-block bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 shift-constraint-btn-save hidden"
              data-id="{{ $shift_constraint->id }}">
              {{ __('Save') }}
            </button>
            <form action="{{ route('shift_constraints.destroy', $shift_constraint) }}" method="POST" class="inline-block">
              @csrf
              @method('DELETE')
              <input type="submit" value="{{ __('Delete') }}"
                onclick="if(!confirm('削除しますか？')){return false};"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="py-4">
      <h3 class="text-lg font-bold">新しいシフト制約を作成</h3>
      <div class="space-y-2">
        <label for="new-shift-constraint-status" class="block">ステータス</label>
        <select id="new-shift-constraint-status" class="border px-4 py-2 rounded w-full">
          <option value="day_off">休みの日</option>
          <option value="mandatory_shift">必須出勤</option>
          <option value="pairing">ペアリング</option>
          <option value="shift_limit">シフト回数制限</option>
        </select>
        <label for="new-shift-constraint-user-id" class="block">ユーザーID</label>
        <input type="number" id="new-shift-constraint-user-id" placeholder="ユーザーID" class="border px-4 py-2 rounded w-full">
        <label for="new-shift-constraint-date" class="block">日付</label>
        <input type="date" id="new-shift-constraint-date" class="border px-4 py-2 rounded w-full">
        <label for="new-shift-constraint-paired-user-id" class="block">ペアリング対象ユーザーID (任意)</label>
        <input type="number" id="new-shift-constraint-paired-user-id" placeholder="ペアリング対象ユーザーID" class="border px-4 py-2 rounded w-full">
        <label for="new-shift-constraint-max-shifts" class="block">最大シフト回数 (任意)</label>
        <input type="number" id="new-shift-constraint-max-shifts" placeholder="最大シフト回数" class="border px-4 py-2 rounded w-full">
        <label for="new-shift-constraint-extra-info" class="block">追加情報 (JSON形式)</label>
        <textarea id="new-shift-constraint-extra-info" placeholder='{"key": "value"}' class="border px-4 py-2 rounded w-full"></textarea>
        <button id="create-shift-constraint-button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2 w-full">
          {{ __('Create Shift Constraint') }}
        </button>
      </div>
    </div>


  </body>

  </html>
  <script src="{{ asset('js/shift_constraint.js') }}"></script>
</x-app-layout>
