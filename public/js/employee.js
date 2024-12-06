// 編集ボタンの動作
function handleEditButtonClick(event) {
    const userId = event.target.dataset.id; // user IDを取得
    const row = document.querySelector(`#employee-row-${userId}`); // 行を取得
    // 表示されている説明文を非表示にし、編集用の<input>を生成
    const descriptionDisplay = row.querySelector('.employee-description-display');
    const descriptionEdit = row.querySelector('.employee-description-edit');
    const currentDescription = descriptionDisplay.textContent.trim();

    // これより下はスキルの説明
    const skillsDisplay = row.querySelector('.employee-skills-display'); // スキル表示部分
    const skillsEdit = row.querySelector('.employee-skills-edit'); // スキル編集部分
    descriptionEdit.innerHTML = `
            <input type="text" class="form-control py-2 px-4 w-full border border-gray-300 rounded-md" value="${currentDescription}">
        `;
    // 編集用フォームを動的に生成
    skillsEdit.innerHTML = [1, 2, 3].map(number => {
        const setId = `${userId}-${number}`; // spanタグのIDを生成
        const span = document.getElementById(setId); // spanタグを取得
        const dataId = span ? span.getAttribute('data-id') : ""; // 属性 "data-id" の値を取得（存在しない場合は空文字）
        console.log(dataId);
        // フォームのHTMLを生成
        return `
        <label for="skill${number}-${userId}" class="block text-sm font-medium text-gray-700">スキル${number}:</label>
        <select id="skill${number}-${userId}" class="form-select w-32 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none">
            <option value="">-- 選択してください --</option>
            ${roles.map(role => `
                <option value="${role.id}" ${role.id == dataId ? "selected" : ""}>${role.name}</option>
            `).join('')}
        </select>
    `;
    }).join('');



    descriptionDisplay.classList.add('hidden'); // 現在の表示を非表示
    descriptionEdit.classList.remove('hidden'); // 編集フィールドを表示
    // 表示/編集の切り替え
    skillsDisplay.classList.add('hidden');
    skillsEdit.classList.remove('hidden');

    // ボタンの切り替え
    event.target.classList.add('hidden'); // 編集ボタンを非表示
    row.querySelector('.employee-btn-save').classList.remove('hidden'); // 保存ボタンを表示
}

// 保存ボタンの動作
function handleSaveButtonClick(event) {
    // ユーザーIDと従業員IDを取得
    const userId = event.target.dataset.id; // user IDを取得
    const employeeId = event.target.dataset.employeeId || null; // employee IDを取得（nullに対応）

    const row = document.querySelector(`#employee-row-${userId}`);
    const updatedDescription = row.querySelector('.employee-description-edit input').value.trim();
    const updatedSkills = {};

    // 各スキルの選択値を取得
    for (let number = 1; number <= 3; number++) {
        const skillElement = document.querySelector(`#skill${number}-${userId}`);
        if (skillElement) {
            updatedSkills[`skill${number}`] = skillElement.value;
        }
    }

    // 保存URLとメソッドを設定
    const url = employeeId ? `/employees/${employeeId}` : `/employees`; // PATCHまたはPOSTのURL
    const method = employeeId ? 'PATCH' : 'POST';
    // 非同期リクエストを送信
    fetch(url, {
        method: method, // メソッドを指定
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            user_id: userId,
            notes: updatedDescription,
            update_skills: updatedSkills,
        }),
    })
        .then(response => {
            if (!response.ok) throw new Error('保存に失敗しました');
            return response.json();
        })
        .then(() => {
            // 従業員概要部分の表示を更新
            row.querySelector('.employee-description-display').textContent = updatedDescription || 'なし';
            row.querySelector('.employee-description-display').classList.remove('hidden');
            row.querySelector('.employee-description-edit').classList.add('hidden');

            // スキル部分の表示を更新
            const skillsDisplay = row.querySelector('.employee-skills-display');
            skillsDisplay.classList.remove('hidden');
            row.querySelector('.employee-skills-edit').classList.add('hidden');

            // 再描画: スキル情報をdata-idとして含む
            skillsDisplay.innerHTML = [1, 2, 3].map(number => {
                const skillId = updatedSkills[`skill${number}`]; // skill1, skill2, skill3 の値を取得
                const role = roles.find(r => r.id === Number(skillId)); // roles から対応する名前を取得

                return `
            <span 
                class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-2"
                id="${userId}-${number}" 
                data-id="${role?.id || ''}">
                ${role?.name || 'なし'}
            </span>
        `;
            }).join('');

            // ボタンの切り替え
            row.querySelector('.employee-btn-edit').classList.remove('hidden');
            event.target.classList.add('hidden');

        })
        .catch(error => {
            alert('エラーが発生しました: ' + error.message);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    // 各ボタンにイベントリスナーを設定
    document.querySelectorAll('.employee-btn-edit').forEach(button => {
        button.addEventListener('click', handleEditButtonClick);
    });

    document.querySelectorAll('.employee-btn-save').forEach(button => {
        button.addEventListener('click', handleSaveButtonClick);
    });
});
