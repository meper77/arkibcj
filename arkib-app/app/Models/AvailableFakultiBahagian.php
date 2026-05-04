<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvailableFakultiBahagian extends Model
{
    protected $table = 'available_fakulti_bahagian';

    protected $fillable = [
        'nama',
        'additional_space_1',
        'additional_space_2',
        'additional_cawangan',
        'fail_am',
        'fail_sulit',
        'fail_pelajar',
        'fail_staff',
        'fail_akademik',
        'fail_pentadbiran',
        'student_id',
        'borang_pemisahan',
        'label_pentadbiran',
        'label_staff',
        'label_pelajar',
    ];

    protected function casts(): array
    {
        return [
            'additional_space_1' => 'boolean',
            'additional_space_2' => 'boolean',
            'additional_cawangan' => 'boolean',
            'fail_am' => 'boolean',
            'fail_sulit' => 'boolean',
            'fail_pelajar' => 'boolean',
            'fail_staff' => 'boolean',
            'fail_akademik' => 'boolean',
            'fail_pentadbiran' => 'boolean',
            'student_id' => 'boolean',
            'borang_pemisahan' => 'boolean',
            'label_pentadbiran' => 'boolean',
            'label_staff' => 'boolean',
            'label_pelajar' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $f) {
            $f->enforceCascade();
        });
    }

    public function enforceCascade(): void
    {
        if (!$this->fail_sulit) {
            $this->fail_pelajar = false;
            $this->fail_staff = false;
        }
        if (!$this->fail_staff) {
            $this->fail_akademik = false;
            $this->fail_pentadbiran = false;
        }
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'fakulti_bahagian_id');
    }

    public function noRujukans(): HasMany
    {
        return $this->hasMany(NoRujukan::class, 'fakulti_bahagian_id');
    }

    public function fails(): HasMany
    {
        return $this->hasMany(Fail::class, 'fakulti_bahagian_id');
    }
}
