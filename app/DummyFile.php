<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DummyFile extends Model
{
    protected $table = 'dummy_files';

    protected $fillable = [
        'path',
        'real_store',
        'dummy_path',
        'dummy_store'
    ];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }
}
