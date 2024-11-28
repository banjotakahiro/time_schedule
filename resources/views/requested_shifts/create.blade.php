<div>
    <h1 class="text-2xl text-center font-semibold p-4">{{ __('Event Form') }}</h1>

    <x-validation-errors class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mx-6" />

    <form action="{{ route('requested_shifts.store', ['user_id' => $user_id]) }}" method="POST" class="relative px-4 pb-4 flex-auto">
        @csrf
        <!-- Event Start -->
        <div class="my-2 text-slate-500 text-base leading-snug">
            <label class="block text-gray-700 text-sm font-semibold mb-1" for="start">
                {{ __('Event Start') }}
            </label>
            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $date) }}"
                class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}"
                class="shadow appearance-none border rounded w-auto py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Event End -->
        <div class="my-2 text-slate-500 text-base leading-snug">
            <label class="block text-gray-700 text-sm font-semibold mb-1" for="end">
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

