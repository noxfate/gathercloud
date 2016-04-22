<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'link_name',
        'user_id',
        'url'
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
}
