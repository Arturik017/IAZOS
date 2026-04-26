<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function respond(Request $request, RefundRequest $refundRequest): RedirectResponse
    {
        $user = auth()->user();

        if (!$user || (int) $refundRequest->seller_id !== (int) $user->id) {
            abort(403, 'Acces interzis.');
        }

        $data = $request->validate([
            'seller_response' => ['required', 'string', 'max:3000'],
            'seller_recommended_status' => ['required', 'in:cancelled,refunded'],
        ]);

        $refundRequest->forceFill([
            'seller_response' => $data['seller_response'],
            'seller_recommended_status' => $data['seller_recommended_status'],
            'seller_responded_at' => now(),
            'status' => 'seller_reviewed',
        ])->save();

        return back()->with('success', 'Raspunsul sellerului a fost salvat. Adminul poate decide mai departe.');
    }
}
