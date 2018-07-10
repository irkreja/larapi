<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded =[];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    // used for App\Tag::find('php') find via name not id
    public function getRouteKeyName()
    {
        return 'name'; //return name column
    }
}
