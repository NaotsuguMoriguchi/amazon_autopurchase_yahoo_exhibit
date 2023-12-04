<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahooOrderItem extends Model
{
    use HasFactory;

    protected $table = 'yahoo_order_items';

    protected $fillable = [
        'user_id',
        'store_id',
        'order_id',
        'item_id',
        'order_time',
        'ship_invoicenumber2',
        'title',
        'quantity',
        'ship_firstname',
        'ship_lastname',
        'unit_price',
        'line_subtotal',
        'total_price',
        'ship_firstname_kana',
        'ship_lastname_kana',
        'ship_zipcode',
        'ship_prefecture',
        'ship_city',
        'ship_address1',
        'ship_address2',
        'ship_phonenumber',
    ];
}
