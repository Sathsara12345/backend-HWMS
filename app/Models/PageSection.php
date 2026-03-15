<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $guarded = [];
    protected $casts = [
        'settings' => 'array',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
