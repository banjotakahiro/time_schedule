<x-app-layout>
  <!DOCTYPE html>
  <html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="bg-blue-50 min-h-screen p-5">
    <div class="container mx-auto bg-white shadow-lg rounded-lg p-6">
      <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">モーダルテスト</h1>

      <!-- モーダルを開くボタン -->
      <button
        type="button"
        class="open-modal bg-blue-500 text-white px-4 py-2 rounded"
        data-date="2000-01-01"
        data-user-id="1">
        モーダルを開く
      </button>
      <!-- モーダルを閉じるボタン -->
      <button
        type="button"
        class="close-modal bg-blue-500 text-white px-4 py-2 rounded"
        data-date="2000-01-01"
        data-user-id="1">
        モーダルを閉じる
      </button>
    </div>

    @php
    $date = "2000-04-01";
    $user_id = "1";
    @endphp
    <!-- モーダルのインクルード -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <!-- モーダルコンテンツ部分 -->
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8">
        @include('requested_shifts.create', ['date' => $date, 'user_id' => $user_id])
      </div>
    </div>


    <!-- 外部JavaScriptファイルの読み込み -->
    <script src="{{ asset('js/script.js') }}"></script>
  </body>

  </html>
</x-app-layout>
