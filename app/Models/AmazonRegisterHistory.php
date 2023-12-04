<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmazonRegisterHistory extends Model
{
    use HasFactory;

    protected $table = 'amazon_register_histories';

    protected $fillable = [
        'user_id',
        'store_id',
        'csv_filename',
        'count',
    ];
}
