<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'user_id', 'hotel_name', 'domain',
        'email', 'phone', 'status'
    ];

    // hotel belongs to a user (the admin account)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}