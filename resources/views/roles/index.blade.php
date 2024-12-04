<x-app-layout>
    <div class="mt-10">
        <h1 class="text-center text-lg">予定一覧</h1>
        <table id="roles-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-3">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="py-3 px-6">仕事の名前</th>
                    <th scope="col" class="py-3 px-6">仕事詳細</th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="py-4 px-6 role-name-display">{{ $employee->user->name }}</td>
                    <td class="py-4 px-6 role-description-display">{{ $employee->user->notes }}</td>
                    @foreach (range(1, 3) as $number)
                    <div class="mb-3">
                        <label for="skill{{ $number }}">スキル{{ $number }}を選択:</label>
                        <select name="skill{{ $number }}" id="skill{{ $number }}" class="form-select">
                            <option value="">-- 選択してください --</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                    <td class="py-4 px-6">
                        <button class="inline-block bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 btn-edit "
                            data-id="{{ $role->id }}">
                            {{ __('Edit') }}
                        </button>
                    </td>
                    <td class="py-4 px-6">
                        <button class="inline-block bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 btn-save hidden"
                            data-id="{{ $role->id }}">
                            {{ __('Save') }}
                        </button>
                    </td>
                    <td class="py-4 px-6">
                        <form action="{{ route('roles.destroy', $role) }}" method="POST">
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

        
        <table id="roles-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-3">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="py-3 px-6">仕事の名前</th>
                    <th scope="col" class="py-3 px-6">仕事詳細</th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                <tr id="role-row-{{ $role->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="py-4 px-6 role-name-display">{{ $role->name }}</td>
                    <td class="py-4 px-6 role-description-display">{{ $role->description }}</td>
                    <td class="py-4 px-6">
                        <button class="inline-block bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 btn-edit "
                            data-id="{{ $role->id }}">
                            {{ __('Edit') }}
                        </button>
                    </td>
                    <td class="py-4 px-6">
                        <button class="inline-block bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 btn-save hidden"
                            data-id="{{ $role->id }}">
                            {{ __('Save') }}
                        </button>
                    </td>
                    <td class="py-4 px-6">
                        <form action="{{ route('roles.destroy', $role) }}" method="POST">
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
            <h3 class="text-lg font-bold">新しい役割を作成</h3>
            <div>
                <input type="text" id="new-role-name" placeholder="役割名" class="border px-4 py-2 rounded">
                <input type="text" id="new-role-description" placeholder="説明" class="border px-4 py-2 rounded">
                <button id="create-role-button"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Create Role') }}
                </button>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/role.js') }}"></script>
</x-app-layout>