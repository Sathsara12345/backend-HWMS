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

    /**
     * Seed default website structure for this hotel
     */
    public function seedDefaults()
    {
        // 1. Seed Navigation Items if empty
        if ($this->navigationItems()->count() === 0) {
            $this->navigationItems()->createMany([
                ['label' => 'Home', 'url' => '/', 'order' => 1],
                ['label' => 'Rooms', 'url' => '/rooms', 'order' => 2],
                ['label' => 'Services', 'url' => '/services', 'order' => 3],
                ['label' => 'About Us', 'url' => '/about', 'order' => 4],
                ['label' => 'Contact', 'url' => '/contact', 'order' => 5],
            ]);
        }

        // 2. Seed Page Sections if empty
        if ($this->pageSections()->count() === 0) {
            $this->pageSections()->createMany([
                [
                    'section_name' => 'Hero',
                    'order' => 1,
                ],
                [
                    'section_name' => 'About',
                    'order' => 2,
                ],
                [
                    'section_name' => 'Services',
                    'order' => 3,
                ],
            ]);
        }
    }
}