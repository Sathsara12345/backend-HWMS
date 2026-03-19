<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PageSection extends Model
{
    protected $fillable = [
        'hotel_id',
        'navigation_item_id',
        'section_name',
        'title',
        'content',
        'order',
        'is_visible',
        'settings',
    ];

    protected $casts = [
        'is_visible'          => 'boolean',
        'order'               => 'integer',
        'settings'            => 'array',
        'navigation_item_id'  => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function navigationItem(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeForHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function scopeForNavItem(Builder $query, ?int $navId): Builder
    {
        return $query->where('navigation_item_id', $navId);
    }
}
