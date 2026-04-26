<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SellerApplication;
use App\Models\SellerCommissionPeriod;
use App\Models\SellerPaymentAccount;
use App\Support\MessageState;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingSellerApplications = SellerApplication::query()->where('status', 'pending')->count();
        $commissionPeriodsAwaitingReview = SellerCommissionPeriod::query()->where('status', 'awaiting_admin_review')->count();
        $commissionPeriodsOverdue = SellerCommissionPeriod::query()->where('status', 'overdue')->count();
        $paymentAccountsPending = SellerPaymentAccount::query()->whereIn('status', ['missing', 'pending', 'rejected'])->count();
        $activeProducts = Product::query()->where('status', 1)->count();

        $messageUnreadCount = 0;
        if (MessageState::supported()) {
            $messageUnreadCount = \App\Models\Conversation::query()
                ->where('admin_id', auth()->id())
                ->withCount([
                    'messages as unread_messages_count' => function ($query) {
                        $query->whereNull('read_at')
                            ->where('sender_id', '!=', auth()->id());
                    },
                ])
                ->get()
                ->sum('unread_messages_count');
        }

        return view('admin.products.dashboard', compact(
            'pendingSellerApplications',
            'commissionPeriodsAwaitingReview',
            'commissionPeriodsOverdue',
            'paymentAccountsPending',
            'activeProducts',
            'messageUnreadCount'
        ));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(\Illuminate\Http\Request $request, Product $product)
    {
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'final_price' => $request->final_price,
            'stock' => $request->stock,
            'status' => $request->status ? 1 : 0,
        ]);

        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index');
    }
}
