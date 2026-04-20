<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pemisahan extends Model
{
    protected $table = 'pemisahan';

    protected $fillable = [
        'fail_id',
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

    public function fail(): BelongsTo
    {
        return $this->belongsTo(Fail::class, 'fail_id');
    }

    public function pelupusan(): HasOne
    {
        return $this->hasOne(Pelupusan::class, 'pemisahan_id');
    }
}
