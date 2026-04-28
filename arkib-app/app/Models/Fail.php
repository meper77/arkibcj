<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fail extends Model
{
    protected $table = 'fail';

    protected $fillable = [
        'no_rujukan_id',
        'jilid',
        'kertas_berhubung_id',
        'tarikh_pertama',
        'tarikh_akhir',
        'tarikh_tutup',
        'kotak',
        'person_in_charge',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_pertama' => 'date',
            'tarikh_akhir' => 'date',
            'tarikh_tutup' => 'date',
            'jilid' => 'integer',
        ];
    }

    public function noRujukan(): BelongsTo
    {
        return $this->belongsTo(NoRujukan::class, 'no_rujukan_id');
    }

    public function pemisahan(): HasOne
    {
        return $this->hasOne(Pemisahan::class, 'fail_id');
    }

    public function kertasBerhubung(): BelongsTo
    {
        return $this->belongsTo(Fail::class, 'kertas_berhubung_id')->with('noRujukan');
    }

    public function getKertasBerhubungLabelAttribute(): ?string
    {
        $rel = $this->kertasBerhubung;
        if (!$rel || !$rel->noRujukan) return null;
        $base = $rel->noRujukan->no_rujukan_full;
        return $rel->jilid > 1 ? "$base Jld.{$rel->jilid}" : $base;
    }
}
