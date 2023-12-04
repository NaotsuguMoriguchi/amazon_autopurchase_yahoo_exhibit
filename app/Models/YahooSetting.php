<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahooSetting extends Model
{
    use HasFactory;

    protected $table = 'yahoo_settings';

    protected $fillable = [
        'user_id',
        'store_id',
        'yahoo_id',
        'yahoo_secret',
        'access_token',
        'id_token',
        'refresh_token',
        'created_refresh_token',
    ];
}
