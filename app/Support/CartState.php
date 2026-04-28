<?php

namespace App\Support;

class CartState
{
    public static function count(): int
    {
        return collect(session('cart', []))
            ->sum(fn ($item) => max(1, (int) ($item['qty'] ?? 0)));
    }
}
