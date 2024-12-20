<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformation_shiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 必要に応じて認可ロジックを追加
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
            'date' => ['nullable', 'date'], // 日付は必須ではない
            'start_time' => ['nullable', 'date_format:H:i'], // HH:MM形式
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'], // 開始時刻より後

            // ロケーション
            'location' => ['nullable', 'string', 'max:255'], // 必須ではない

            // スキルと必要人数
            'skill1' => ['nullable', 'integer', 'exists:skills,id'], // スキル1の外部キー
            'required_staff_skill1' => ['nullable', 'integer', 'min:1'], // 1以上
            'skill2' => ['nullable', 'integer', 'exists:skills,id'], // スキル2の外部キー
            'required_staff_skill2' => ['nullable', 'integer', 'min:1'], // 1以上
            'skill3' => ['nullable', 'integer', 'exists:skills,id'], // スキル3の外部キー
            'required_staff_skill3' => ['nullable', 'integer', 'min:1'], // 1以上
        ];
    }
}
