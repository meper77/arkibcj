<?php

namespace App\Models;

use App\Models\Scopes\BelongsToFakulti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelupusan extends Model
{
    protected $table = 'pelupusan';

    protected $fillable = [
        'pemisahan_id',
        'no_rujukan_id',
        'jilid',
        'fakulti_bahagian_id',
        'kotak',
        'tajuk_fail',
        'status',
        'person_in_charge',
        'lupus_at',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BelongsToFakulti());
    }

    protected function casts(): array
    {
        return [
            'lupus_at' => 'datetime',
        ];
    }

    public function pemisahan(): BelongsTo
    {
        return $this->belongsTo(Pemisahan::class, 'pemisahan_id');
    }

    public function isLupused(): bool
    {
        return $this->lupus_at !== null;
    }
}
