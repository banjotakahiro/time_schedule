// シフト制約を新規作成する関数
function createShiftConstraint() {
    // 入力欄から値を取得
    const status = document.getElementById('new-shift-constraint-status').value;
    const userId = document.getElementById('new-shift-constraint-user-id').value.trim();
    const date = document.getElementById('new-shift-constraint-date').value;
    const pairedUserId = document.getElementById('new-shift-constraint-paired-user-id').value.trim();
    const maxShifts = document.getElementById('new-shift-constraint-max-shifts').value.trim();
    const extraInfo = document.getElementById('new-shift-constraint-extra-info').value.trim();

    // 入力検証
    if (!status || !userId) {
        alert('ステータスとユーザーIDは必須です');
        return;
    }

    // JSON形式のオブジェクトを構築
    const requestData = {
        status: status,
        user_id: userId,
        date: date || null, // 日付が空の場合はnullを送信
        paired_user_id: pairedUserId || null, // ペアリングユーザーが空の場合はnullを送信
        max_shifts: maxShifts || null, // 最大シフト回数が空の場合はnullを送信
        extra_info: extraInfo ? JSON.parse(extraInfo) : null, // JSON形式で保存
    };

    // 非同期リクエストでデータを保存
    fetch('/shift_constraints', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // CSRFトークンを追加
        },
        body: JSON.stringify(requestData), // リクエストボディにJSONデータを含める
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('シフト制約の作成に失敗しました');
            }
            return response.json(); // レスポンスをJSONとして解析
        })
        .then(data => {
            alert('シフト制約が正常に作成されました');

            // 入力欄をリセット
            document.getElementById('new-shift-constraint-status').value = '';
            document.getElementById('new-shift-constraint-user-id').value = '';
            document.getElementById('new-shift-constraint-date').value = '';
            document.getElementById('new-shift-constraint-paired-user-id').value = '';
            document.getElementById('new-shift-constraint-max-shifts').value = '';
            document.getElementById('new-shift-constraint-extra-info').value = '';

            // テーブルを再描画
            reloadShiftConstraintsTable();
        })
        .catch(error => {
            console.error('エラー:', error);
            alert('シフト制約の作成中にエラーが発生しました: ' + error.message);
        });
}

// テーブルを非同期的に再描画する関数
function reloadShiftConstraintsTable() {
    // シフト制約データを取得
    fetch('/shift_constraints')
        .then(response => {
            if (!response.ok) {
                throw new Error('テーブルの再読み込みに失敗しました');
            }
            return response.text(); // レスポンスをHTMLとして取得
        })
        .then(html => {
            const parser = new DOMParser();
            const newTable = parser.parseFromString(html, 'text/html').querySelector('#shift_constraints-table');
            const currentTable = document.querySelector('#shift_constraints-table');
            currentTable.replaceWith(newTable); // 古いテーブルを新しいテーブルに置き換え
        })
        .catch(error => {
            console.error('エラー:', error);
            alert('テーブルの再描画中にエラーが発生しました');
        });
}

// DOMが読み込まれたときにリスナーを設定
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('create-shift-constraint-button').addEventListener('click', createShiftConstraint);
});
