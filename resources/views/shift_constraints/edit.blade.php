<div class="container">
    <h2>シフト設定編集</h2>
    <form id="edit-shift-constraint-form" action="{{ route('shift_constraints.update', $shift_constraint->id) }}" method="POST">
        @csrf
        @method('PATCH') <!-- PATCH メソッドを指定 -->

        <div class="form-group mb-3">
            <label for="status">ステータス</label>
            <select id="status" name="status" class="form-control" onchange="toggleFields(this.value)">
                <option value="day_off" {{ $shift_constraint->status == 'day_off' ? 'selected' : '' }}>休みの日</option>
                <option value="mandatory_shift" {{ $shift_constraint->status == 'mandatory_shift' ? 'selected' : '' }}>必須出勤</option>
                <option value="pairing" {{ $shift_constraint->status == 'pairing' ? 'selected' : '' }}>一緒にしていい人</option>
                <option value="no_pairing" {{ $shift_constraint->status == 'no_pairing' ? 'selected' : '' }}>一緒にしたらだめな人</option>
                <option value="shift_limit" {{ $shift_constraint->status == 'shift_limit' ? 'selected' : '' }}>シフト回数制限</option>
            </select>
        </div>

        <div id="fields-container">
            <!-- ユーザーID -->
            <div class="form-group mb-3 user-id-field">
                <label for="user_id">ユーザーID</label>
                <select id="user_id" name="user_id" class="form-control">
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $shift_constraint->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- ペアユーザーID -->
            <div class="form-group mb-3 paired-user-id-field">
                <label for="paired_user_id">ペアユーザーID</label>
                <select id="paired_user_id" name="paired_user_id" class="form-control">
                    <option value="">未設定</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $shift_constraint->paired_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 開始日 -->
            <div class="form-group mb-3 start-date-field">
                <label for="start_date">開始日</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $shift_constraint->start_date }}">
            </div>

            <!-- 終了日 -->
            <div class="form-group mb-3 end-date-field">
                <label for="end_date">終了日</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $shift_constraint->end_date }}">
            </div>

            <!-- シフト回数制限 -->
            <div class="form-group mb-3 max-shifts-field">
                <label for="max_shifts">シフト回数制限</label>
                <input type="number" id="max_shifts" name="max_shifts" class="form-control" value="{{ $shift_constraint->max_shifts }}">
            </div>

            <!-- 役割 -->
            <div class="form-group mb-3 role-field">
                <label for="role">役割</label>
                <select id="role" name="role" class="form-control">
                    <option value="">未選択</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $shift_constraint->role == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 優先度 -->
            <div class="form-group mb-3 priority-field">
                <label for="priority">優先度</label>
                <input type="number" id="priority" name="priority" class="form-control" value="{{ $shift_constraint->priority }}">
            </div>

            <!-- その他の情報 -->
            <div class="form-group mb-3 extra-info-field">
                <label for="extra_info">その他の情報</label>
                <textarea id="extra_info" name="extra_info" class="form-control">{{ $shift_constraint->extra_info }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
