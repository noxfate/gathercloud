<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'connection_name',
        'access_token',
        'access_token_expired',
        'refresh_token',
        'refresh_token_expired',
        'user_id',
        'provider'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
//    protected $hidden = ['password', 'remember_token'];
}
