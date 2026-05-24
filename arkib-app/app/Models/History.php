<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class History extends Model
{
    protected $table = 'histories';

    protected $fillable = [
        'user_id',
        'user_name',
        'fakulti_bahagian_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('belongsToFakultiHistory', function (Builder $builder) {
            $user = Auth::user();
            if (!$user) {
                return;
            }
            if ($user->is_superadmin && !$user->fakulti_bahagian_id) {
                return;
            }
            if ($user->is_superadmin) {
                $builder->where(function ($q) use ($user) {
                    $q->where('histories.fakulti_bahagian_id', $user->fakulti_bahagian_id)
                      ->orWhereNull('histories.fakulti_bahagian_id');
                });
                return;
            }
            $builder->where('histories.fakulti_bahagian_id', $user->fakulti_bahagian_id);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fakultiBahagian(): BelongsTo
    {
        return $this->belongsTo(AvailableFakultiBahagian::class, 'fakulti_bahagian_id');
    }

    public static function log(string $action, ?string $description = null, ?string $entityType = null, $entityId = null, ?int $fakultiBahagianId = null): void
    {
        $user = Auth::user();
        self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'fakulti_bahagian_id' => $fakultiBahagianId ?? $user?->fakulti_bahagian_id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
        ]);
    }
}
