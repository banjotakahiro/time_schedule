<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformation_shiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 必要に応じて認可のロジックを追加
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 日付と時間
            'date' => ['required', 'date'], // 必須項目で有効な日付
            'start_time' => ['required', 'date_format:H:i'], // HH:MM形式の時刻
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'], // 開始時刻より後

            // ロケーション
            'location' => ['required', 'string', 'max:255'], // 必須、文字列、最大255文字

            // スキルと必要人数
            'skill1' => ['nullable', 'integer', 'exists:skills,id'], // 必須でないが整数かつスキルIDの外部キー
            'required_staff_skill1' => ['nullable', 'integer', 'min:1'], // 必須でないが1以上の整数
            'skill2' => ['nullable', 'integer', 'exists:skills,id'],
            'required_staff_skill2' => ['nullable', 'integer', 'min:1'],
            'skill3' => ['nullable', 'integer', 'exists:skills,id'],
            'required_staff_skill3' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
