<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahooStoreItem extends Model
{
    use HasFactory;

    protected $table = 'yahoo_store_items';

    protected $fillable = [
        'user_id',
        'store_id',
        'amazon_category',
        'yahoo_category',
        'name',
        'caption',
        'dimension',
        'item_code',
        'asin',
        'jan',
        'amazon_price',
        'yahoo_price',
        'img_url',
        'shop_url',
        'stock',
        'is_updated',
    ];
}
