<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Token extends Model
{
    public $guarded = [];

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function hasExpired()
    {
        // has the token expires since last time it updated
        return Carbon::now()->gte($this->updated_at->addSeconds($this->expires_in));
    }
}
