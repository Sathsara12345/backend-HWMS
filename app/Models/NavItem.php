<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavItem extends Model
{
    protected $fillable = [
        'label', 'href', 'icon', 'section',
        'parent_id', 'order', 'is_active'
    ];

    // child nav items (e.g. Users, Roles under User Management)
    public function children()
    {
        return $this->hasMany(NavItem::class, 'parent_id')
                    ->where('is_active', true)
                    ->orderBy('order');
    }

    // roles that can see this nav item
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'nav_item_role');
    }
}
