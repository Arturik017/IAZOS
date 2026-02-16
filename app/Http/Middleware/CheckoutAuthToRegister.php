<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckoutAuthToRegister
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            // după register/login îl întoarce înapoi la checkout
            session()->put('url.intended', route('checkout.index'));

            return redirect()->route('register')
                ->with('error', 'Pentru a finaliza comanda, creează cont sau autentifică-te.');
        }

        return $next($request);
    }
}
