<?php

namespace App\Models;

use App\Models\Scopes\BelongsToFakulti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'fakulti_bahagian_id',
        'jenis_fail',
        'kategori',
        'sub_kategori',
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

    protected static function booted(): void
    {
        static::addGlobalScope(new BelongsToFakulti());
    }

    public function noRujukan(): BelongsTo
    {
        return $this->belongsTo(NoRujukan::class, 'no_rujukan_id');
    }

    public function pemisahan(): HasOne
    {
        return $this->hasOne(Pemisahan::class, 'fail_id');
    }

    public function fakultiBahagian(): BelongsTo
    {
        return $this->belongsTo(AvailableFakultiBahagian::class, 'fakulti_bahagian_id');
    }

    public function kertasBerhubung(): BelongsTo
    {
        return $this->belongsTo(Fail::class, 'kertas_berhubung_id')->with('noRujukan');
    }

    public function studentIds(): HasMany
    {
        return $this->hasMany(FailStudentId::class, 'fail_id');
    }

    public function getKertasBerhubungLabelAttribute(): ?string
    {
        $rel = $this->kertasBerhubung;
        if (!$rel || !$rel->noRujukan) return null;
        $base = $rel->noRujukan->no_rujukan_full;
        return $rel->jilid > 1 ? "$base Jld.{$rel->jilid}" : $base;
    }
}
