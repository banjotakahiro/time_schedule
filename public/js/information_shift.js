// モーダルを開くロジック
async function openModal(date, modal) {
    try {
        // サーバーからデータを取得
        const response = await fetch(`/information_shifts/create?date=${date}`);
        const html = await response.text();

        // モーダルにデータを挿入
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = html;

        // モーダルを表示
        modal.classList.remove('hidden');
    } catch (error) {
        console.error('エラー:', error); // エラーハンドリング
    }
}

// モーダルを閉じるロジック
function closeModalOnBackgroundClick(modal, event) {
    if (event.target === modal) {
        modal.classList.add('hidden');
    }
}

// 初期化処理
function initializeModal() {
    const modal = document.getElementById('modal');

    // テーブルのすべてのセルにクリックイベントを付与
    document.querySelectorAll('td[data-date]').forEach(td => {
        td.addEventListener('click', function () {
            const date = td.getAttribute('data-date'); // data-date属性から日付を取得
            openModal(date, modal);
        });
    });

    // 背景クリックでモーダルを閉じる
    modal.addEventListener('click', function (event) {
        closeModalOnBackgroundClick(modal, event);
    });
}

// DOMContentLoaded イベントリスナー
document.addEventListener('DOMContentLoaded', function () {
    initializeModal();
});
