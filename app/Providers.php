<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Providers extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'provider_name',
        'provider_logo',
        'reference_name'
    ];


    public function token()
    {
        return $this->hasMany(Token::class);
    }
}
