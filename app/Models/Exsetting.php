<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exsetting extends Model
{
    use HasFactory;

    protected $table = 'exhibit_settings';

    protected $fillable = [
        'user_id',
        'amazon_setting',
        'yahoo_setting',
        'not_asin',
        'not_word',
        'remove_word',
        'invalid_word',
        'price_settings',
        'commission',
        'expenses',
    ];

    // public function user() {
    //     return $this->belongsTo(
    //         User::class,
    //         'user_id'
    //     );
    // }
}
