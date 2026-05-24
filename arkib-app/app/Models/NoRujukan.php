<?php

namespace App\Models;

use App\Models\Scopes\BelongsToFakulti;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoRujukan extends Model
{
    protected $table = 'no_rujukan';

    protected $fillable = [
        'siri',
        'kampus',
        'kod_bahagian',
        'nombor_fail',
        'perkara',
        'deskripsi',
        'additional_space',
        'fakulti_bahagian_id',
    ];

    protected function casts(): array
    {
        return [
            'additional_space' => 'boolean',
            'siri' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new BelongsToFakulti());
    }

    public function fails(): HasMany
    {
        return $this->hasMany(Fail::class, 'no_rujukan_id');
    }

    public function fakultiBahagian(): BelongsTo
    {
        return $this->belongsTo(AvailableFakultiBahagian::class, 'fakulti_bahagian_id');
    }

    public function getNoRujukanFullAttribute(): string
    {
        $fb = $this->fakultiBahagian;
        // ON = ada ruang. OFF = aksara dirapatkan (no space).
        $space1 = $fb ? (bool) $fb->additional_space_1 : false;
        $space2 = $fb ? (bool) $fb->additional_space_2 : (bool) $this->additional_space;
        $cawangan = $fb ? (bool) $fb->additional_cawangan : false;

        $sep = $space2 ? '. ' : '.';
        if ($cawangan) {
            $inner = $this->kod_bahagian . $sep . '(S)' . $this->nombor_fail;
        } else {
            $inner = $this->kod_bahagian . $sep . $this->nombor_fail;
        }

        $gap = $space1 ? ' ' : '';
        return $this->siri . '-' . $this->kampus . $gap . '(' . $inner . ')';
    }
}
