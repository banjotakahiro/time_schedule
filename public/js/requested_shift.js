document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal');
    const openModalButtons = document.querySelectorAll('.open-modal'); // cdボタンのクラスを基準に取得

    // モーダルを開く
    openModalButtons.forEach(button => {
        button.addEventListener('click', async function (event) {
            event.preventDefault();
            const date = button.getAttribute('data-date'); // data-date の値を取得
            const userId = button.getAttribute('data-user-id'); // data-user-id の値を取得

        try {
            // サーバーからデータを取得
            const response = await fetch(`/requested_shifts/create?date=${date}&user_id=${userId}`);
            const html = await response.text();

            // モーダルにデータを挿入
            const modalContent = document.getElementById('modal-content');
            modalContent.innerHTML = html;

            // モーダルを表示
            modal.classList.remove('hidden');
        } catch (error) {
            console.error('エラー:', error); // エラーハンドリング
        }
        });
    });

    // 背景クリックでモーダルを閉じる
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
