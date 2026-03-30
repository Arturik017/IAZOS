<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\SellerReview;
use App\Models\User;
use Illuminate\Http\Request;

class SellerReviewController extends Controller
{
    public function store(Request $request, User $user)
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:3000'],
        ]);

        $hasPurchasedFromSeller = OrderItem::query()
            ->where('seller_id', $user->id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('payment_status', 'paid');
            })
            ->exists();

        if (!$hasPurchasedFromSeller) {
            return back()->with('error', 'Poți lăsa review sellerului doar dacă ai cumpărat de la el.');
        }

        SellerReview::updateOrCreate(
            [
                'seller_id' => $user->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $request->integer('rating'),
                'comment' => $request->input('comment'),
            ]
        );

        return back()->with('success', 'Review-ul pentru seller a fost salvat.');
    }
}