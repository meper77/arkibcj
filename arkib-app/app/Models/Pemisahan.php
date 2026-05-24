<?php

namespace App\Models;

use App\Models\Scopes\BelongsToFakulti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pemisahan extends Model
{
    protected $table = 'pemisahan';

    protected $fillable = [
        'fail_id',
        'fakulti_bahagian_id',
        'tarikh_pemisahan',
        'tujuan_pemisahan',
        'person_in_charge',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_pemisahan' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new BelongsToFakulti());
    }

    public function fail(): BelongsTo
    {
        return $this->belongsTo(Fail::class, 'fail_id');
    }

    public function pelupusan(): HasOne
    {
        return $this->hasOne(Pelupusan::class, 'pemisahan_id');
    }

    public function fakultiBahagian(): BelongsTo
    {
        return $this->belongsTo(AvailableFakultiBahagian::class, 'fakulti_bahagian_id');
    }
}
