<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
