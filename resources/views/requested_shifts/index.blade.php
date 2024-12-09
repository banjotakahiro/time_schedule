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
    @php
    $date = "2024-12-09";
    $user_id = 2;
    @endphp

    <div>
      <h1 class="text-2xl text-center font-semibold p-4">{{ __('Event Form') }}</h1>

      <x-validation-errors class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mx-6" />

      <form action="{{ route('requested_shifts.store', ['user_id' => $user_id]) }}" method="POST" class="relative px-4 pb-4 flex-auto">
        @csrf
        <!-- Event Start -->
        <div class="my-2 text-slate-500 text-base leading-snug">
          <label class="block text-gray-700 text-sm font-semibold mb-1" for="start_date">
            {{ __('Event Start') }}
          </label>
          <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $date) }}"
            class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}"
            class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Event End -->
        <div class="my-2 text-slate-500 text-base leading-snug">
          <label class="block text-gray-700 text-sm font-semibold mb-1" for="end_date">
            {{ __('Event End') }}
          </label>
          <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $date) }}"
            class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
            class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Event Name -->
        <div class="my-2 text-slate-500 text-base leading-snug">
          <label class="block text-gray-700 text-sm font-semibold mb-1" for="title">
            {{ __('Event Name') }}
          </label>
          <input type="text" name="title" id="title" placeholder="{{ __('Event Name') }}"
            value="{{ old('title') }}"
            class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Description -->
        <div class="my-2 text-slate-500 text-base leading-snug">
          <label class="block text-gray-700 text-sm font-semibold mb-1" for="body">
            {{ __('Description') }}
          </label>
          <textarea name="body" id="body" placeholder="{{ __('Description') }}"
            class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24">{{ old('body') }}</textarea>
        </div>

        <!-- Submit -->
        <input type="submit" value="{{ __('Create') }}"
          class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline">
      </form>
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
  <script src="{{ asset('js/requested_shift.js') }}"></script>
</x-app-layout>
