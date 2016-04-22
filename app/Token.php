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
        'connection_email',
        'access_token',
        'access_token_expired',
        'refresh_token',
        'gtc_folder',
        'user_id',
        'provider_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */


    public function file()
    {
        return $this->hasMany(File::class);
    }

    public function dummyFile()
    {
        return $this->belongsTo(DummyFile::class);
    }
}
