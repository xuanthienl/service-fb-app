<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facebook extends Model
{
    use SoftDeletes;
    protected $table = 'facebook';
    protected $fillable = [
        'user_id', 'uid', 'type', 'status', 'channel', 'amount', 'speed', 'content', 'image', 'total_payment'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo('App\User', 'id');
    }
}
