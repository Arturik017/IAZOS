<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (($user->role ?? null) !== 'admin' && (int)($user->is_admin ?? 0) !== 1) {
            abort(403, 'Acces interzis.');
        }

        return $next($request);
    }
}