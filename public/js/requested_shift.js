// モーダルを開くロジック
async function handleClick(tdElement) {
    const date = tdElement.getAttribute('data-date'); // data-date の値を取得
    const userId = tdElement.getAttribute('data-user-id'); // data-user-id の値を取得

    try {
        // サーバーからデータを取得
        const response = await fetch(`/requested_shifts/create?date=${date}&user_id=${userId}`);
        const html = await response.text();

        // モーダルにデータを挿入
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = html;

        // モーダルを表示
        const modal = document.getElementById('modal'); // モーダルのIDが必要
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
    const openModalButtons = document.querySelectorAll('.open-modal'); // ボタンのクラスを基準に取得

    // 各ボタンにクリックイベントを付与
    openModalButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            handleClick(button); // openModalをhandleClickに置き換え
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
