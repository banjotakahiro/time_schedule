document.addEventListener('DOMContentLoaded', function () {
    const roles = @json($roles); // PHPのデータをJavaScriptで使用

    function handleEditButtonClick(event) {
        const rowId = event.target.dataset.id;
        const row = document.querySelector(`#role-row-${rowId}`);
        const descriptionEdit = row.querySelector('.description-edit');
        const skillsEdit = row.querySelector('.skills-edit');

        // 説明の編集フィールドを生成
        const currentDescription = event.target.dataset.description || '';
        descriptionEdit.innerHTML = `<label for="description-${rowId}">説明を編集:</label>
            <input type="text" id="description-${rowId}" class="form-control py-2 px-4 w-full border border-gray-300 rounded-md" value="${currentDescription}">`;

        // スキルの編集フィールドを生成
        const currentSkills = JSON.parse(event.target.dataset.skills || '[]');
        skillsEdit.innerHTML = '';
        currentSkills.forEach((skill, index) => {
            const skillNumber = index + 1;
            let selectHTML = `<label for="skill${skillNumber}-${rowId}">スキル${skillNumber}を選択:</label>
                <select id="skill${skillNumber}-${rowId}" class="form-select w-full border border-gray-300 rounded-md">
                    <option value="">-- 選択してください --</option>`;
            roles.forEach(role => {
                const selected = skill == role.id ? 'selected' : '';
                selectHTML += `<option value="${role.id}" ${selected}>${role.name}</option>`;
            });
            selectHTML += '</select>';
            skillsEdit.innerHTML += selectHTML;
        });

        // 表示を編集モードに切り替え
        row.querySelector('.description-display').classList.add('hidden');
        descriptionEdit.classList.remove('hidden');
        row.querySelector('.skills-display').classList.add('hidden');
        skillsEdit.classList.remove('hidden');

        // ボタンの切り替え
        event.target.classList.add('hidden');
        row.querySelector('.btn-save').classList.remove('hidden');
    }

    function handleSaveButtonClick(event) {
        const rowId = event.target.dataset.id;
        const row = document.querySelector(`#role-row-${rowId}`);

        // 入力値を取得
        const newDescription = row.querySelector(`#description-${rowId}`).value;
        const newSkills = [];
        row.querySelectorAll('.skills-edit select').forEach(select => {
            newSkills.push(select.value);
        });

        // 表示を通常モードに戻す
        row.querySelector('.description-display').textContent = newDescription || 'なし';
        row.querySelector('.description-display').classList.remove('hidden');
        row.querySelector('.description-edit').classList.add('hidden');

        const skillsDisplay = row.querySelector('.skills-display');
        skillsDisplay.innerHTML = '';
        newSkills.forEach(skillId => {
            if (skillId) {
                const skillName = roles.find(role => role.id == skillId)?.name || '不明';
                skillsDisplay.innerHTML += `<span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-2">${skillName}</span>`;
            } else {
                skillsDisplay.innerHTML += `<span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-2">なし</span>`;
            }
        });
        skillsDisplay.classList.remove('hidden');
        row.querySelector('.skills-edit').classList.add('hidden');

        // ボタンの切り替え
        row.querySelector('.btn-edit').classList.remove('hidden');
        event.target.classList.add('hidden');
    }

    // ボタンにイベントリスナーを設定
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', handleEditButtonClick);
    });
    document.querySelectorAll('.btn-save').forEach(button => {
        button.addEventListener('click', handleSaveButtonClick);
    });
});
