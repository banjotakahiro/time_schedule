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


  // 非同期リクエストでデータを保存処理する。なお、updateメソッドではない
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
  // 非同期リクエストで最新のテーブルHTMLを取得
  fetch('/confirmed_shifts')
    .then(response => {
      if (!response.ok) {
        throw new Error('テーブルデータの再読み込みに失敗しました');
      }
      return response.text(); // レスポンスをHTMLとして取得
    })
    .then(html => {
      const parser = new DOMParser();
      const newTable = parser.parseFromString(html, 'text/html').querySelector('#shift_constraints-table');

      // 現在のテーブルと置き換え
      const currentTable = document.querySelector('#shift_constraints-table');
      if (currentTable && newTable) {
        currentTable.replaceWith(newTable);

        // 必要に応じてイベントリスナーを再設定
        initializeTableEventListeners();
      }
    })
    .catch(error => {
      console.error('エラー:', error.message);
    });
}

function initializeTableEventListeners() {
  // 編集ボタンや保存ボタンのイベントリスナーを再設定する
  document.querySelectorAll('.shift-constraint-btn-edit').forEach(button => {
    button.addEventListener('click', function (event) {
      console.log('Edit button clicked', event.target.dataset.id);
      // 必要に応じて編集モードのロジックを追加
    });
  });

  document.querySelectorAll('.shift-constraint-btn-save').forEach(button => {
    button.addEventListener('click', function (event) {
      console.log('Save button clicked', event.target.dataset.id);
      // 必要に応じて保存処理のロジックを追加
    });
  });
}

// シフト制約を上書きしてupdateする処理

async function saveShiftConstraint(rowId, updatedData) {
  try {
    console.log('送信データ:', updatedData); // 送信データを表示

    const response = await fetch(`/shift_constraints/${rowId}`, {
      method: 'PATCH', // データを更新
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify(updatedData),
    });

    if (!response.ok) {
      throw new Error(`保存中にエラーが発生しました: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('保存が成功しました:', result);
    alert('シフト制約が正常に保存されました');
  } catch (error) {
    console.error('保存中にエラーが発生しました:', error);
    alert(`保存に失敗しました: ${error.message}`);
  }
}


// クリックしたら編集ボタンが出てくる処理

function initializeShiftConstraintHandlers() {
  const editButtons = document.querySelectorAll('.shift-constraint-btn-edit');
  const saveButtons = document.querySelectorAll('.shift-constraint-btn-save');
  const deleteForms = document.querySelectorAll('.shift-constraint-delete-form');

  function toggleInputFields(row, isEditMode) {
    row.querySelectorAll('td').forEach(cell => {
      if (cell.classList.contains('shift-constraint-status-display') ||
        cell.classList.contains('shift-constraint-user-id-display') ||
        cell.classList.contains('shift-constraint-date-display') ||
        cell.classList.contains('shift-constraint-paired-user-id-display') ||
        cell.classList.contains('shift-constraint-max-shifts-display') ||
        cell.classList.contains('shift-constraint-extra-info-display')) {

        if (isEditMode) {
          const currentValue = cell.textContent.trim();
          const input = document.createElement('input');
          input.type = 'text';
          input.value = currentValue;
          input.className = 'py-2 px-4 border rounded w-full';
          cell.textContent = '';
          cell.appendChild(input);
        } else {
          const input = cell.querySelector('input');
          if (input) {
            const newValue = input.value.trim();
            cell.textContent = newValue;
          }
        }
      }
    });
  }

  editButtons.forEach(editButton => {
    editButton.addEventListener('click', () => {
      const rowId = editButton.getAttribute('data-id');
      const row = document.getElementById(`shift-constraint-row-${rowId}`);

      // 入力欄を表示する
      toggleInputFields(row, true);

      // ボタンの表示を切り替える
      editButton.classList.add('hidden');
      const saveButton = row.querySelector(`.shift-constraint-btn-save`);
      saveButton.classList.remove('hidden');

      const deleteForm = row.querySelector('.shift-constraint-delete-form');
      deleteForm.classList.add('hidden');
    });
  });

  saveButtons.forEach(saveButton => {
    saveButton.addEventListener('click', async () => {
      const rowId = saveButton.getAttribute('data-id');
      const row = document.getElementById(`shift-constraint-row-${rowId}`);

      // 入力欄を元に戻す
      toggleInputFields(row, false);

      // 更新された値を収集
      const updatedData = {};
      row.querySelectorAll('td').forEach(cell => {
        const input = cell.querySelector('input');
        const className = cell.className.split(' ').find(cls => cls.startsWith('shift-constraint-'));

        if (className) {
          // `-display` を削除して正しいキー名に変換
          const key = className.replace('shift-constraint-', '').replace('-display', '');
          if (input) {
            updatedData[key] = input.value.trim(); // <input>タグの値を取得
          } else {
            updatedData[key] = cell.textContent.trim(); // セルのテキストを取得
          }
        }
      });

      console.log('収集されたデータ:', updatedData);

      // 非同期で保存を実行
      await saveShiftConstraint(rowId, updatedData);

      // ボタンの表示を切り替える
      saveButton.classList.add('hidden');
      const editButton = row.querySelector(`.shift-constraint-btn-edit`);
      editButton.classList.remove('hidden');

      const deleteForm = row.querySelector('.shift-constraint-delete-form');
      deleteForm.classList.remove('hidden');
    });
  });
}


// DOMが読み込まれたときにリスナーを設定
document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('create-shift-constraint-button').addEventListener('click', createShiftConstraint);
  initializeShiftConstraintHandlers();
});
