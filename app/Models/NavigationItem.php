<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $guarded = [];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
