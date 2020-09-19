<?php

namespace Modules\HttpHeaderAuth\Entities;

use Illuminate\Database\Eloquent\Model;

class HttpUser extends Model
{
    protected $fillable = ['remote_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
