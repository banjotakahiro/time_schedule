// 編集ボタンの動作
function RoleEditButtonClick(event) {
    const roleId = event.target.dataset.id; // `this`ではなく`event.target`を使用
    const row = document.querySelector(`#role-row-${roleId}`);

    // 各セルの内容を取得
    const nameCell = row.querySelector('.role-name-display');
    const descCell = row.querySelector('.role-description-display');

    // 現在のテキスト値を取得
    const originalName = nameCell.textContent.trim();
    const originalDesc = descCell.textContent.trim();

    // セルを入力フィールドに置き換える
    nameCell.innerHTML = `<input type="text" id="name" class="form-control role-name-input py-2 px-4 w-full" value="${originalName}">`;
    descCell.innerHTML = `<input type="text" id="description" class="form-control role-description-input py-2 px-4 w-full" value="${originalDesc}">`;

    // ボタンを保存モードに切り替え
    event.target.classList.add('hidden'); // 編集ボタンを非表示
    row.querySelector('.role-btn-save').classList.remove('hidden'); // 保存ボタンを表示
}
// 保存ボタンの動作
function RoleSaveButtonClick(event) {
    const roleId = event.target.dataset.id;
    const row = document.querySelector(`#role-row-${roleId}`);

    // 入力値を取得
    const updatedName = row.querySelector('.role-name-input').value.trim();
    const updatedDesc = row.querySelector('.role-description-input').value.trim();

    console.log(updatedName);
    console.log(updatedDesc);
    if (!updatedName || !updatedDesc) {
        alert('役割名と説明を入力してください');
        return;
    }

    // 非同期で保存リクエストを送信
    fetch(`/roles/${roleId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            name: updatedName,
            description: updatedDesc,
        }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('保存に失敗しました');
            }
            return response.json();
        })
        .then(data => {
            // セルを更新して通常表示に戻す
            row.querySelector('.role-name-display').innerHTML = updatedName;
            row.querySelector('.role-description-display').innerHTML = updatedDesc;

            // ボタンを編集モードに戻す
            row.querySelector('.role-btn-save').classList.add('hidden'); // 保存ボタンを非表示
            row.querySelector('.role-btn-edit').classList.remove('hidden'); // 編集ボタンを表示
        })
        .catch(error => {
            alert('エラーが発生しました: ' + error.message);
        });
}

// ここから下は新しいrolesを作成する際に必要となる処理！！



function reloadTable() {
    fetch('/roles')
        .then(response => {
            if (!response.ok) {
                throw new Error('テーブルの再読み込みに失敗しました');
            }
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const newTable = parser.parseFromString(html, 'text/html').querySelector('#roles-table');
            const currentTable = document.querySelector('#roles-table');
            currentTable.replaceWith(newTable);

        })
        .catch(error => {
            console.error('エラー:', error);
            alert('テーブルの再描画中にエラーが発生しました');
        });
}

function attachEventListeners() {
    document.querySelectorAll('.role-btn-edit').forEach(button => {
        button.addEventListener('click', RoleEditButtonClick);
    });

    document.querySelectorAll('.role-btn-save').forEach(button => {
        button.addEventListener('click', RoleSaveButtonClick);
    });
}

function createRole(roleName, roleDescription) {
    fetch('/roles', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            name: roleName,
            description: roleDescription,
        }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('役割の作成に失敗しました');
            }
            return response.json();
        })
        .then(data => {
            alert('役割が正常に作成されました');
            document.getElementById('new-role-name').value = '';
            document.getElementById('new-role-description').value = '';

            reloadTable();
        })
        .catch(error => {
            console.error('エラー:', error);
            alert('役割の作成中にエラーが発生しました');
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('create-role-button').addEventListener('click', () => {
        const nameInput = document.getElementById('new-role-name').value.trim();
        const descriptionInput = document.getElementById('new-role-description').value.trim();

        if (!nameInput || !descriptionInput) {
            alert('役割名と説明を入力してください');
            return;
        }

        createRole(nameInput, descriptionInput);
    });
    attachEventListeners();

});
