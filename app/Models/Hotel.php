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
            $homeNav = $this->navigationItems()->where('label', 'Home')->first();
            $homeId = $homeNav?->id;

            $this->pageSections()->createMany([
                [
                    'section_name' => 'hero',
                    'navigation_item_id' => $homeId,
                    'order' => 1,
                    'data_source' => 'static',
                ],
                [
                    'section_name' => 'experience',
                    'navigation_item_id' => $homeId,
                    'order' => 2,
                    'data_source' => 'static',
                ],
                [
                    'section_name' => 'featured_rooms',
                    'navigation_item_id' => $homeId,
                    'order' => 3,
                    'data_source' => 'rooms',
                    'settings' => ['limit' => 3]
                ],
                [
                    'section_name' => 'booking_banner',
                    'navigation_item_id' => $homeId,
                    'order' => 4,
                    'data_source' => 'static',
                ],
            ]);
        }
    }
}