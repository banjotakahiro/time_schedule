    <div>
      <h1 class="text-2xl text-center font-semibold p-4">{{ __('Event Form') }}</h1>

      <x-validation-errors class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mx-6" />

      <form action="{{ route('requested_shifts.store', ['user_id' => $user_id]) }}" method="POST" class="relative px-4 pb-4 flex-auto">
        @csrf
        <!-- Event Start -->
        <div class="my-2 text-slate-500 text-base leading-snug">
          <label class="block text-gray-700 text-sm font-semibold mb-1" for="date">
            {{ __('Event Start') }}
          </label>
          <input type="date" name="date" id="date" value="{{ old('date', $date) }}"
            class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- 時間範囲選択 -->
        <div class="my-2 text-slate-500 text-base leading-snug">
          <label class="block text-gray-700 text-sm font-semibold mb-1" for="time_range">
            時間範囲を選択
          </label>
          <select id="time_range" class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="08:00~12:00">08:00~12:00</option>
            <option value="12:00~16:00">12:00~16:00</option>
            <option value="16:00~20:00">16:00~20:00</option>
            <option value="20:00~22:00">20:00~22:00</option>
          </select>
        </div>

        <input type="hidden" name="start_time" id="start_time">
        <input type="hidden" name="end_time" id="end_time">
        <!-- Submit -->
        <input type="submit" value="{{ __('Create') }}"
          class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline">
      </form>
    </div>

