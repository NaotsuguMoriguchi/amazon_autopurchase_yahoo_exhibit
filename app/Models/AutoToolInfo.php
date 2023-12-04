<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoToolInfo extends Model
{
    use HasFactory;

    protected $table = 'auto_tool_infos';

    protected $fillable = [
        'user_id',
        'tool_key',
        'period_days',
        'register_date',
        'expired_date',
        'access_count',
    ];
}
