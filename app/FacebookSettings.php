<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacebookSettings extends Model
{
    use SoftDeletes;
    protected $table = 'facebook-settings';
    protected $fillable = [
        'type', 'channel_type', 'channel_name', 'channel_price', 'channel_description', 'amount_min', 'amount_max', 'speed_min', 'speed_max'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
