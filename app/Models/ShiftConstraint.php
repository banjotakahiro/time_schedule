<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftConstraint extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',           // ステータス
        'user_id',          // ユーザーID
        'date',             // 日付
        'paired_user_id',   // ペアリング対象ユーザーID
        'max_shifts',       // 最大シフト回数
        'role',       // 役割の指定
        'priority',       // 優先順位の設定
        'extra_info',       // 追加情報 (JSON形式)
    ];

    // リレーションの定義
    public function roleDetails()
    {
        return $this->belongsTo(Role::class, 'role'); // 'role'は外部キー名
    }
}
