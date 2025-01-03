// DOMが読み込まれたときの初期化
document.addEventListener('DOMContentLoaded', () => {
  initializeTableEventListeners();

  // 初期表示で適切なフィールドを表示
  const statusField = document.querySelector('#status');
  if (statusField) {
    toggleFields(statusField.value);
    statusField.addEventListener('change', (event) => {
      toggleFields(event.target.value);
    });
  }

  const newStatusField = document.querySelector('#new-shift-constraint-status');
  if (newStatusField) {
    toggleFields(newStatusField.value, 'new');
    newStatusField.addEventListener('change', (event) => {
      toggleFields(event.target.value, 'new');
    });
  }
});

// モーダルを開くロジック
async function openModal(button) {
  const shiftConstraintId = button.dataset.id;
  const modalContent = document.getElementById('modal-content');
  const modal = document.getElementById('modal');

  try {
    // 非同期で edit.blade.php の内容を取得
    const response = await fetch(`/shift_constraints/${shiftConstraintId}/edit`);
    if (!response.ok) throw new Error('Failed to load the edit view.');

    // モーダルに内容を挿入
    modalContent.innerHTML = await response.text();

    // ステータス変更イベントリスナーを設定
    const statusField = modalContent.querySelector('#status');
    if (statusField) {
      toggleFields(statusField.value);
      statusField.addEventListener('change', (event) => {
        toggleFields(event.target.value);
      });
    }

    // モーダルを表示
    modal.classList.remove('hidden');
  } catch (error) {
    modalContent.innerHTML = '<p class="text-danger">編集フォームの読み込みに失敗しました。</p>';
    console.error(error);
  }
}

// モーダルを閉じるロジック
function closeModal() {
  const modal = document.getElementById('modal');
  modal.classList.add('hidden');
}

// テーブルの編集ボタンイベントを再設定
function initializeTableEventListeners() {
  const editButtons = document.querySelectorAll('.shift-constraint-btn-edit');

  editButtons.forEach(button => {
    button.addEventListener('click', () => openModal(button));
  });

  // モーダル背景クリックで閉じる
  const modal = document.getElementById('modal');
  modal.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
  });
}

// ステータスに基づいてフィールドをトグルする関数
function toggleFields(status, formType = 'edit') {
  const allFields = {
    'day_off': ['user-id-field', 'start-date-field', 'end-date-field', 'priority-field', 'extra-info-field'],
    'mandatory_shift': ['user-id-field', 'start-date-field', 'end-date-field', 'priority-field', 'role-field', 'extra-info-field'],
    'pairing': ['user-id-field', 'start-date-field', 'end-date-field', 'paired-user-id-field', 'priority-field', 'extra-info-field'],
    'no_pairing': ['user-id-field', 'start-date-field', 'end-date-field', 'paired-user-id-field', 'priority-field', 'extra-info-field'],
    'shift_limit': ['user-id-field', 'start-date-field', 'end-date-field', 'max-shifts-field', 'priority-field', 'extra-info-field']
  };

  const containerId = formType === 'edit' ? '#fields-container' : '#new-shift-constraint-fields';

  // 全フィールドを非表示
  document.querySelectorAll(`${containerId} .form-group`).forEach(field => {
    field.style.display = 'none';
  });

  // 必要なフィールドを表示
  if (allFields[status]) {
    allFields[status].forEach(cls => {
      const field = document.querySelector(`${containerId} .${cls}`);
      if (field) field.style.display = 'block';
    });
  }
}
