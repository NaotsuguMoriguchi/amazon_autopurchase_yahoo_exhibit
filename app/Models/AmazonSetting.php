<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonSetting extends Model
{
    use HasFactory;

    protected $table = 'amazon_settings';

    protected $fillable = [
        'user_id',
        'store_id',
        'access_key',
        'secret_key',
        'partner_tag',
        'life_check',
    ];
}
