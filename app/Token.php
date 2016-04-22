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
        'refresh_token_expired',
        'user_id',
        'provider',
        'gtc_folder'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cache()
    {
        return $this->hasOne(Cache::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function dummyFiles()
    {
        return $this->belongsTo(DummyFile::class);
    }
}
