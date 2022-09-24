<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSettings extends Model
{
    use SoftDeletes;
    protected $table = 'system-settings';
    protected $fillable = [
        'bot_token', 'bot_name', 'chat_id', 'chat_type', 'chat_title', 'payment_type', 'account_name', 'bank_name', 'bank_code', 'bank_branch'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
