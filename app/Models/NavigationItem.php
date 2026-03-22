<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class NavigationItem extends Model
{
    protected $guarded = [];

    // ─── Relationships ────────────────────────────────────────

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function pageSections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('order');
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeForHotel(Builder $query, int $hotelId): Builder
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderBy('id');
    }
}
