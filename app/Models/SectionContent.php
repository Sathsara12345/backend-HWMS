<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionContent extends Model
{
    protected $fillable = ['section_id', 'field_key', 'field_value', 'type'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'section_id');
    }

    public function getCastedValueAttribute(): mixed
    {
        return match ($this->type) {
            'json'   => json_decode($this->field_value, true),
            'number' => is_numeric($this->field_value) ? (float) $this->field_value : null,
            default  => $this->field_value,
        };
    }
}
