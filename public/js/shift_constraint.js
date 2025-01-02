// シフト制約を新規作成する関数
function createShiftConstraint() {
  // 入力欄から値を取得
  const status = document.getElementById('new-shift-constraint-status').value;
  const userId = document.getElementById('new-shift-constraint-user-id').value.trim();
  const start_date = document.getElementById('new-shift-constraint-start-date').value;
  const end_date = document.getElementById('new-shift-constraint-end-date').value;
  const pairedUserId = document.getElementById('new-shift-constraint-paired-user-id').value.trim();
  const role = document.getElementById('new-shift-constraint-role').value.trim();
  const maxShifts = document.getElementById('new-shift-constraint-max-shifts').value.trim();
  const extraInfo = document.getElementById('new-shift-constraint-extra-info').value.trim();

  // 入力検証
  if (!status || !userId) {
    alert('ステータスとユーザーIDは必須です');
    return;
  }

  // リクエストデータを構築
  const requestData = {
    status,
    user_id: userId,
    start_date,
    end_date,
    paired_user_id: pairedUserId || null,
    role: role || null,
    max_shifts: maxShifts || null,
    extra_info: extraInfo, // ここでJSON文字列として渡す
  };

  // 非同期リクエストでデータを保存処理する
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
      document.getElementById('new-shift-constraint-start-date').value = '';
      document.getElementById('new-shift-constraint-end-date').value = '';
      document.getElementById('new-shift-constraint-paired-user-id').value = '';
      document.getElementById('new-shift-constraint-max-shifts').value = '';
      document.getElementById('new-shift-constraint-role').value = ''; // 役割もリセット
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

  const statusTranslations = {
    'day_off': '休みの日',
    'mandatory_shift': '必須出勤',
    'pairing': '一緒にしていい人',
    'no_pairing': '一緒にしたらだめな人',
    'shift_limit': 'シフト回数制限'
  };

  function translateStatus(value, toJapanese = true) {
    if (toJapanese) {
      return statusTranslations[value] || value;
    }
    return Object.keys(statusTranslations).find(key => statusTranslations[key] === value) || value;
  }

  function toggleInputFields(row, isEditMode) {
    row.querySelectorAll('td').forEach(cell => {
      if (
        cell.classList.contains('shift-constraint-status-display') ||
        cell.classList.contains('shift-constraint-user-id-display') ||
        cell.classList.contains('shift-constraint-start-date-display') ||
        cell.classList.contains('shift-constraint-end-date-display') ||
        cell.classList.contains('shift-constraint-paired-user-id-display') ||
        cell.classList.contains('shift-constraint-max-shifts-display') ||
        cell.classList.contains('shift-constraint-priority-display') ||
        cell.classList.contains('shift-constraint-role-display') ||
        cell.classList.contains('shift-constraint-extra-info-display')
      ) {
        const currentValue = cell.textContent.trim(); // 現在のセルの値を取得

        if (isEditMode) {
          // 編集モードの設定
          if (cell.classList.contains('shift-constraint-status-display')) {
            // ステータス用のドロップダウン
            const select = document.createElement('select');
            select.className = 'py-2 px-4 border rounded w-full';
            [
              { value: 'day_off', label: '休みの日' },
              { value: 'mandatory_shift', label: '必須出勤' },
              { value: 'pairing', label: '一緒にしていい人' },
              { value: 'no_pairing', label: '一緒にしたらだめな人' },
              { value: 'shift_limit', label: 'シフト回数制限' }
            ].forEach(optionData => {
              const option = document.createElement('option');
              option.value = optionData.value;
              option.textContent = optionData.label;
              if (optionData.label === currentValue) {
                option.selected = true;
              }
              select.appendChild(option);
            });
            cell.textContent = '';
            cell.appendChild(select);
          } else if (cell.classList.contains('shift-constraint-user-id-display') || cell.classList.contains('shift-constraint-paired-user-id-display')) {
            // ユーザー用のドロップダウン
            const select = document.createElement('select');
            select.className = 'py-2 px-4 border rounded w-full';
            select.innerHTML = users.map(user => `
            <option value="${user.id}" ${user.name === currentValue ? "selected" : ""}>${user.name}</option>
          `).join('');
            cell.textContent = '';
            cell.appendChild(select);
          } else if (cell.classList.contains('shift-constraint-role-display')) {
            // 役割用のドロップダウン
            const select = document.createElement('select');
            select.className = 'py-2 px-4 border rounded w-full';
            select.innerHTML = roles.map(role => `
            <option value="${role.id}" ${role.name === currentValue ? "selected" : ""}>${role.name}</option>
          `).join('');
            cell.textContent = '';
            cell.appendChild(select);
          } else {
            // その他のテキスト入力
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentValue;
            input.className = 'py-2 px-4 border rounded w-full';
            cell.textContent = '';
            cell.appendChild(input);
          }
        }
        // ここより下を直せば入力欄を閉じたときの表示の処理はどうにかなる
        else {
          const inputOrSelect = cell.querySelector('input, select');
          if (inputOrSelect) {
            let newValue;

            if (inputOrSelect.tagName === 'SELECT') {
              // ドロップダウン形式の場合、labelを取得
              const selectedOption = inputOrSelect.options[inputOrSelect.selectedIndex];
              newValue = selectedOption ? selectedOption.textContent.trim() : ''; // 選択されたオプションのラベル
            } else if (inputOrSelect.tagName === 'INPUT') {
              // 通常の入力欄の場合、値を取得
              newValue = inputOrSelect.value.trim();
            }

            if (cell.classList.contains('shift-constraint-status-display')) {
              // ステータスを変換して表示
              newValue = translateStatus(newValue, false);
              cell.textContent = cell.dataset.selectedLabel || translateStatus(newValue, true);
            } else {
              // 取得した値またはラベルをセルに設定
              cell.textContent = newValue;
            }
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

      // 更新された値と更新されていない値も収集
      const updatedData = {};

      // やっと入力欄ベースで値を取得できるようになった！！
      row.querySelectorAll('td').forEach(cell => {
        const classNameArray = cell.className.split(' ');
        const className = classNameArray.find(cls => cls.startsWith('shift-constraint-'));

        if (className) {
          try {
            // `-display` を削除して正しいキー名に変換
            const key = className.replace('shift-constraint-', '').replace('-display', '');

            const inputOrSelect = cell.querySelector('input, select'); // 入力欄またはセレクトボックスを取得

            if (inputOrSelect) {
              // 入力欄やセレクトボックスから値を取得
              if (inputOrSelect.tagName === 'SELECT') {
                updatedData[key] = inputOrSelect.value; // セレクトボックスの値を取得
              } else if (inputOrSelect.tagName === 'INPUT') {
                updatedData[key] = inputOrSelect.value.trim(); // 入力欄の値を取得
              }
            } else {
              updatedData[key] = cell.textContent.trim(); // 入力欄がない場合はテキストを取得
            }

            console.log(updatedData[key]);
          } catch (error) {
            console.error(`エラーが発生しました: ${error.message}`);
          }
        }
      });

      console.log('収集されたデータ:', updatedData);

      // 非同期で保存を実行
      await saveShiftConstraint(rowId, updatedData);
      // ここで入力欄を元に戻している
      // 入力欄を元に戻す
      // この入力欄をもとに戻す際の処理があそこの関数に書かれている
      toggleInputFields(row, false);
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
