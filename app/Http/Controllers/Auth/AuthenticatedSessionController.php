<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\GuestWishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        $guestWishlist = $request->session()->get(GuestWishlist::SESSION_KEY, []);

        $request->authenticate();

        $request->session()->regenerate();

        if (!empty($cart)) {
            $request->session()->put('cart', $cart);
        }

        if (!empty($guestWishlist)) {
            $request->session()->put(GuestWishlist::SESSION_KEY, $guestWishlist);
        }

        GuestWishlist::mergeIntoUser($request->user());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
