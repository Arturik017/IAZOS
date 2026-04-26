<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class SellerFollowController extends Controller
{
    public function store(User $user): RedirectResponse
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        if (!User::supportsSellerFollowers()) {
            return back()->with('error', 'Functia de urmarit selleri va fi activa dupa rularea migrarilor noi.');
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Nu te poti urmari pe tine ca seller.');
        }

        auth()->user()->followedSellers()->syncWithoutDetaching([$user->id]);

        return back()->with('success', 'Sellerul a fost adaugat la urmarite.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        if (!User::supportsSellerFollowers()) {
            return back()->with('error', 'Functia de urmarit selleri va fi activa dupa rularea migrarilor noi.');
        }

        auth()->user()->followedSellers()->detach($user->id);

        return back()->with('success', 'Sellerul a fost eliminat din urmarite.');
    }
}
