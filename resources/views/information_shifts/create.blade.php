<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-xl text-center font-semibold mb-4">{{ __('Event Form') }}</h1>

    <x-validation-errors class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-4" />

    <form action="{{ route('information_shifts.store') }}" method="POST" class="space-y-4">
        @csrf
        <!-- Event Start & End -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Event Start -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                <input type="date" name="date" id="date" value="{{ old('date', $date) }}"
                    class="w-full border rounded-md p-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
            </div>
            <!-- Start and End Time in a Row -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">{{ __('Start Time') }}</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}"
                        class="w-full border rounded-md p-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">{{ __('End Time') }}</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
                        class="w-full border rounded-md p-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Location -->
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700">{{ __('Location') }}</label>
            <input type="text" name="location" id="location" placeholder="{{ __('Location') }}" value="{{ old('location') }}"
                class="w-full border rounded-md p-2 text-sm focus:ring focus:ring-blue-200 focus:outline-none">
        </div>

        <!-- Skills -->
        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Skill Requirements') }}</h2>
            <div class="grid grid-cols-3 gap-4">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="p-2 border rounded-md bg-gray-50">
                    <label for="skill{{ $i }}" class="block text-xs font-medium text-gray-700 mb-1">
                        {{ __("Skill $i") }}
                    </label>
                    <select id="skill{{ $i }}" name="skill{{ $i }}"
                        class="w-full border rounded-md p-2 text-xs focus:ring focus:ring-blue-200 focus:outline-none">
                        <option value="">{{ __('-- Select --') }}</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <label for="required_staff_skill{{ $i }}" class="block text-xs font-medium text-gray-700 mt-2">
                        {{ __("Required Staff") }}
                    </label>
                    <input type="number" name="required_staff_skill{{ $i }}" id="required_staff_skill{{ $i }}"
                        value="{{ old('required_staff_skill' . $i) }}"
                        class="w-full border rounded-md p-2 text-xs focus:ring focus:ring-blue-200 focus:outline-none">
            </div>
            @endfor
        </div>

        <!-- Submit -->
        <input type="submit" value="{{ __('Create') }}"
            class="w-full bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 rounded-md focus:outline-none focus:ring focus:ring-blue-200">
    </form>
</div>
