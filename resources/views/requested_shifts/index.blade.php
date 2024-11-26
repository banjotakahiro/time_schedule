<x-app-layout>

  <!DOCTYPE html>
  <html lang="en">

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
              $day_of_week = ["月","火","水","木","金","土","日"]
            @endphp
            @foreach (range(0, 6) as $day) <!-- 0から6までの範囲をループ -->
              @php
                $date = $startDate->copy($day)->addDays($day);
              @endphp
              <th class="border border-blue-300 px-4 py-2 bg-blue-200 text-blue-900">{{ $date->format('d日') }} {{ '(' . $day_of_week[$day] .')' }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
              <tr>
                <td class="border border-blue-300 px-4 py-2 bg-blue-100 text-blue-900">{{ $user -> name }}</td>
                @foreach ($users as $user)
                  <td class="shift-cell border border-blue-300 px-4 py-2 bg-white hover:bg-blue-100 cursor-pointer">{{ optional($user->requested_shift)->first()->title ?? "シフトが見つかりません" }}</td>
                @endforeach
              </tr>
            @endforeach

        </tbody>
      </table>
    </div>
    <script src="script.js"></script>
  </body>

  </html>

  <!-- <div class="mt-10">
        <h1 class="text-center text-lg">予定一覧</h1>

        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-3">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="py-3 px-6">予定まで</th>
                    <th scope="col" class="py-3 px-6">開始</th>
                    <th scope="col" class="py-3 px-6">タイトル</th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requested_shifts as $requested_shift)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="py-4 px-6">今回は書かない</td>
                    <td class="py-4 px-6">{{$requested_shift -> start}}</td>
                    <td class="py-4 px-6">{{$requested_shift -> title}}</td>
                    <td class="py-4 px-6">
                        <a href="{{ route('requested_shifts.show' ,$requested_shift)}}"
                            class="inline-block bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                            {{ __('Details') }}
                        </a>
                    </td>
                    <td class="py-4 px-6"><a href="{{ route('requested_shifts.edit' ,$requested_shift)}}"
                            class="inline-block bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                            {{ __('Edit') }}
                        </a>
                    </td>
                    <td class="py-4 px-6">
                        <form action="{{ route('requested_shifts.destroy' ,$requested_shift)}}" method="POST">
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
    </div> -->
</x-app-layout>
