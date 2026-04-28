<?php

namespace App\Support;

use Illuminate\Support\Collection;

class WishlistState
{
    private static ?Collection $ids = null;
    private static ?Collection $items = null;

    public static function items(): Collection
    {
        if (self::$items !== null) {
            return self::$items;
        }

        if (!auth()->check()) {
            self::$items = GuestWishlist::items()
                ->map(fn ($item) => [
                    'product_id' => (int) $item['product_id'],
                    'variant_id' => $item['variant_id'] ? (int) $item['variant_id'] : null,
                ])
                ->values();

            return self::$items;
        }

        self::$items = auth()->user()
            ->wishlistItems()
            ->get(['product_id', 'variant_id'])
            ->map(fn ($item) => [
                'product_id' => (int) $item->product_id,
                'variant_id' => $item->variant_id ? (int) $item->variant_id : null,
            ])
            ->values();

        return self::$items;
    }

    public static function ids(): Collection
    {
        if (!auth()->check()) {
            return self::items()
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();
        }

        if (self::$ids !== null) {
            return self::$ids;
        }

        self::$ids = self::items()
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        return self::$ids;
    }

    public static function count(): int
    {
        return self::ids()->count();
    }

    public static function has(int $productId): bool
    {
        return self::ids()->contains($productId);
    }

    public static function hasVariant(int $productId, ?int $variantId): bool
    {
        return self::items()->contains(function ($item) use ($productId, $variantId) {
            return (int) $item['product_id'] === $productId
                && (int) ($item['variant_id'] ?? 0) === (int) ($variantId ?? 0);
        });
    }
}
