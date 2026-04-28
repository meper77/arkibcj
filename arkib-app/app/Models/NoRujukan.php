<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected function casts(): array
    {
        return [
            'additional_space' => 'boolean',
            'siri' => 'integer',
        ];
    }

    public function fails(): HasMany
    {
        return $this->hasMany(Fail::class, 'no_rujukan_id');
    }

    public function getNoRujukanFullAttribute(): string
    {
        $space = $this->additional_space ? ' ' : '';
        return $this->siri . '-' . $this->kampus . $space . '(' . $this->kod_bahagian . '. ' . $this->nombor_fail . ')';
    }
}
