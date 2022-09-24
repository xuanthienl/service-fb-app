<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $table = 'payment';
    protected $fillable = [
        'user_id', 'code', 'amount_coin', 'price', 'status'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
