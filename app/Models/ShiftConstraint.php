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
        'extra_info',       // 追加情報 (JSON形式)
    ];
}
