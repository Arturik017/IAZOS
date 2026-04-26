<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:3000'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $hasPurchased = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('payment_status', 'paid');
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Doar utilizatorii care au cumpărat produsul pot lăsa review.');
        }

        $review = ProductReview::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $request->integer('rating'),
                'comment' => $request->input('comment'),
            ]
        );

        if ($request->hasFile('images')) {
            foreach ($review->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            foreach ($request->file('images') as $file) {
                $path = ImageStorage::storeWebp($file, 'product-reviews', 'public', 80, 'images');

                $review->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        return back()->with('success', 'Review-ul a fost salvat.');
    }
}
