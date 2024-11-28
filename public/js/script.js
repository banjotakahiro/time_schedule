document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal');
    const closeModalButtons = document.querySelectorAll('.close-modal');
    const openModalButtons = document.querySelectorAll('.open-modal'); // ボタンのクラスを基準に取得

    // モーダルを開く
    openModalButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const date = button.getAttribute('data-date'); // data-date の値を取得
            const userId = button.getAttribute('data-user-id'); // data-user-id の値を取得

            console.log(userId);
            // モーダルを表示
            modal.classList.remove('hidden');
        });
    });

    // モーダルを閉じる
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function (event) {
          event.preventDefault();
          modal.classList.add('hidden');
        });
    });

    // 背景クリックでモーダルを閉じる
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
