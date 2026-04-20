<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelupusan extends Model
{
    protected $table = 'pelupusan';

    protected $fillable = [
        'pemisahan_id',
        'kotak',
        'tajuk_fail',
        'status',
        'person_in_charge',
        'lupus_at',
    ];

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
