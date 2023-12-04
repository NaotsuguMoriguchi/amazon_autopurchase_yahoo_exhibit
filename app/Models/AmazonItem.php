<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonItem extends Model
{
    use HasFactory;

    protected $table = 'amazon_items';

    protected $fillable = [
        'user_id',
        'store_id',
        'category',
        'caption',
        'dimension',
        'asin',
        'jan',
        'am_price',
        'img_url',
        'shop_url',
        'exhibit',
    ];
}
