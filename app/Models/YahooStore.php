<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahooStore extends Model
{
    use HasFactory;

    protected $table = 'yahoo_stores';

    protected $fillable = [
        'user_id',
        'store_name',
        'order_count',
    ];
}
