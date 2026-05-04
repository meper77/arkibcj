<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWriteAccess
{
    /**
     * Allow write actions only for users with PTRJ/PRJ position or superadmin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Akses ditolak.');
        }
        if ($user->is_superadmin) {
            return $next($request);
        }
        if (in_array($user->position, ['PTRJ', 'PRJ'], true)) {
            return $next($request);
        }
        abort(403, 'Hanya pengguna berjawatan PTRJ atau PRJ dibenarkan untuk tindakan ini.');
    }
}
