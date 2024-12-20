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
        const modal = document.getElementById('modal');
        modal.classList.remove('hidden');

        // モーダルにtime_range_selector.jsの処理を適用
        initializeTimeRangeSelector();

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
            handleClick(button);
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

// Time Range Selector の初期化処理
// ここでselectした時間をstart_timeとend_timeに分けて保存できるようにしている
function initializeTimeRangeSelector() {
  const timeRangeElement = document.getElementById('time_range');
  const startTimeElement = document.getElementById('start_time');
  const endTimeElement = document.getElementById('end_time');

  // デフォルト値を設定
  if (startTimeElement && endTimeElement) {
    startTimeElement.value = '08:00';
    endTimeElement.value = '12:00';
  }

  // time_rangeが存在する場合のみイベントリスナーを設定
  if (timeRangeElement) {
    timeRangeElement.addEventListener('change', function () {
      // 選択された値を取得して分割
      const selectedValue = this.value; // 例: "08:00~12:00"
      const [startTime, endTime] = selectedValue.split('~'); // "08:00" と "12:00"

      // 開始時間と終了時間を設定
      if (startTime && endTime) {
        startTimeElement.value = startTime;
        endTimeElement.value = endTime;
        console.log(`Start Time: ${startTime}, End Time: ${endTime}`);
      } else {
        console.error('Invalid time range selected');
      }
    });
  } else {
    console.error('time_range element not found in the DOM');
  }
}
