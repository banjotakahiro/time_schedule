<x-app-layout>
    <div class="mt-10">
        <h1 class="text-center text-lg">予定一覧</h1>
        <table id="employees-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-3">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="py-3 px-6">従業員の名前</th>
                    <th scope="col" class="py-3 px-6">従業員概要</th>
                    <th scope="col" class="py-3 px-6">スキル</th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr id="employee-row-{{ $user->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <!-- 従業員の名前 -->
                    <td class="py-4 px-6 employee-name">{{ $user->name }}</td>

                    <!-- 従業員の説明 -->
                    <td class="py-4 px-6">
                        <div class="employee-description-display">{{ $user->employee->notes ?? 'なし' }}</div>
                        <div class="employee-description-edit hidden"></div>
                    </td>

                    <!-- 従業員のスキル -->
                    <td class="py-4 px-6">
                        <div class="employee-skills-display">
                            @if ($user->employee)
                            @foreach (range(1, 3) as $number)
                            <span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-2">
                                {{ $roles->firstWhere('id', $user->employee->{'skill' . $number})?->name ?? 'なし' }}
                            </span>
                            @endforeach
                            @else
                            <span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-2">なし</span>
                            @endif
                        </div>
                    </td>

                    <!-- 編集ボタン -->
                    <td class="py-4 px-6">
                        <button class="employee-btn-edit bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded"
                            data-id="{{ $user->id }}" data-has-employee="{{ $user->employee ? 'true' : 'false' }}">
                            {{ __('Edit') }}
                        </button>
                    </td>

                    <!-- 保存ボタン -->
                    <td class="py-4 px-6">
                        <button class="employee-btn-save bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded hidden"
                            data-id="{{ $user->id }}"
                            data-employee-id="{{ $user->employee->id ?? null }}">
                            {{ __('Save') }}
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>





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
                    <button class="inline-block bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 role-btn-edit "
                        data-id="{{ $role->id }}">
                        {{ __('Edit') }}
                    </button>
                </td>
                <td class="py-4 px-6">
                    <button class="inline-block bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 role-btn-save hidden"
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
    <script src="{{ asset('js/employee.js') }}"></script>
</x-app-layout>
