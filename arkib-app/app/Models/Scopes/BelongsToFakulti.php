<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BelongsToFakulti implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }
        // Superadmin with no fakulti set sees everything.
        if ($user->is_superadmin && !$user->fakulti_bahagian_id) {
            return;
        }
        $builder->where($model->getTable() . '.fakulti_bahagian_id', $user->fakulti_bahagian_id);
    }
}
