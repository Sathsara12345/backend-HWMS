<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionTemplate extends Model
{
    protected $fillable = ['name', 'schema'];

    protected $casts = ['schema' => 'array'];

    public function pageSections(): HasMany
    {
        return $this->hasMany(PageSection::class, 'template_id');
    }
}
