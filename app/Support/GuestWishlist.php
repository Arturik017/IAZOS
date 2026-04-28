<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class GuestWishlist
{
    public const SESSION_KEY = 'guest_wishlist';

    public static function items(): Collection
    {
        return collect(session(self::SESSION_KEY, []))
            ->map(function ($item, $rowId) {
                return [
                    'row_id' => (string) ($item['row_id'] ?? $rowId),
                    'product_id' => (int) ($item['product_id'] ?? 0),
                    'variant_id' => isset($item['variant_id']) ? (int) $item['variant_id'] : null,
                ];
            })
            ->filter(fn ($item) => $item['product_id'] > 0)
            ->values();
    }

    public static function count(): int
    {
        return self::items()->count();
    }

    public static function has(int $productId): bool
    {
        return self::items()->contains(fn ($item) => (int) $item['product_id'] === $productId);
    }

    public static function hasVariant(int $productId, ?int $variantId): bool
    {
        return self::items()->contains(function ($item) use ($productId, $variantId) {
            return (int) $item['product_id'] === $productId
                && (int) ($item['variant_id'] ?? 0) === (int) ($variantId ?? 0);
        });
    }

    public static function add(int $productId, ?int $variantId = null): void
    {
        $rowId = self::makeRowId($productId, $variantId);
        $items = collect(session(self::SESSION_KEY, []));

        $items = $items->reject(function ($item) use ($productId) {
            return (int) ($item['product_id'] ?? 0) === $productId;
        });

        $items->put($rowId, [
            'row_id' => $rowId,
            'product_id' => $productId,
            'variant_id' => $variantId,
        ]);

        session()->put(self::SESSION_KEY, $items->all());
    }

    public static function remove(int $productId, ?int $variantId = null): void
    {
        $rowId = self::makeRowId($productId, $variantId);
        $items = collect(session(self::SESSION_KEY, []));
        $items->forget($rowId);
        session()->put(self::SESSION_KEY, $items->all());
    }

    public static function removeRows(array $rowIds): void
    {
        $items = collect(session(self::SESSION_KEY, []));

        foreach ($rowIds as $rowId) {
            $items->forget((string) $rowId);
        }

        session()->put(self::SESSION_KEY, $items->all());
    }

    public static function mergeIntoUser(User $user): void
    {
        $items = self::items();

        foreach ($items as $item) {
            $user->wishlistItems()->firstOrCreate([
                'product_id' => (int) $item['product_id'],
                'variant_id' => $item['variant_id'],
            ]);
        }

        self::clear();
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function makeRowId(int $productId, ?int $variantId = null): string
    {
        return $productId . '-' . ($variantId ?: 'base');
    }
}
