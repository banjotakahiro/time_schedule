<x-app-layout>
    <div class="mt-10">
        <h1 class="text-center text-lg">予定一覧</h1>

        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mt-3">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="py-3 px-6">仕事の名前</th>
                    <th scope="col" class="py-3 px-6">仕事詳細</th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                    <th scope="col" class="py-3 px-6"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="py-4 px-6">{{ $job -> name}}</td>
                        <td class="py-4 px-6"></td>
                        <td class="py-4 px-6">
                            <a href=""
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                                {{ __('Details') }}
                            </a>
                        </td>
                        <td class="py-4 px-6"><a href=""
                                class="inline-block bg-green-500 hover:bg-green-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                                {{ __('Edit') }}
                            </a>
                        </td>
                        <td class="py-4 px-6">
                            <form action="" method="POST">
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
    </div>
</x-app-layout>
