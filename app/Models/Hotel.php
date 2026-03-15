<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function navigationItems()
    {
        return $this->hasMany(NavigationItem::class);
    }

    public function pageSections()
    {
        return $this->hasMany(PageSection::class);
    }
}