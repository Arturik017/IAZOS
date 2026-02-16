<?php
    /** @var \App\Models\Product $product */
    $url = route('product.show', $product);
?>

<div
    class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden hover:shadow-lg transition cursor-pointer select-none"
    role="link"
    tabindex="0"
    onclick="window.location.href='<?php echo e($url); ?>'"
    onkeydown="if(event.key === 'Enter'){ window.location.href='<?php echo e($url); ?>' }"
>
    
    <div class="relative">
        <?php if($product->image): ?>
            <img src="<?php echo e(asset('storage/'.$product->image)); ?>"
                 class="h-44 w-full object-cover"
                 alt="<?php echo e($product->name); ?>">
        <?php else: ?>
            <div class="h-44 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                Fără imagine
            </div>
        <?php endif; ?>

        
        <form
            method="POST"
            action="<?php echo e(route('cart.add', $product)); ?>"
            class="absolute top-3 right-3"
            onclick="event.stopPropagation();"
        >
            <?php echo csrf_field(); ?>
            <button
                type="submit"
                class="w-11 h-11 rounded-full bg-white/95 hover:bg-white shadow border border-gray-200 flex items-center justify-center"
                title="Adaugă în coș"
                aria-label="Adaugă în coș"
                onclick="event.stopPropagation();"
            >
                
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M6 6h15l-1.5 9h-13L5 3H2"/>
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M9 22a1 1 0 100-2 1 1 0 000 2zM18 22a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
            </button>
        </form>

        
        <?php if((int)($product->is_promo ?? 0) === 1): ?>
            <div class="absolute bottom-3 left-3">
                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                    Promo
                </span>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="p-5">
        <div class="font-semibold text-gray-900 leading-snug line-clamp-2">
            <?php echo e($product->name); ?>

        </div>

        <div class="mt-2 flex items-end justify-between gap-3">
            <div class="text-xl font-extrabold text-gray-900">
                <?php echo e(number_format($product->final_price, 2)); ?> MDL
            </div>

            <div class="text-sm text-gray-500">
                Stoc: <span class="font-semibold text-gray-900"><?php echo e($product->stock); ?></span>
            </div>
        </div>

        
        <div class="mt-3 text-xs text-gray-400">
            Click pe card pentru detalii
        </div>
    </div>
</div>
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/partials/product-card.blade.php ENDPATH**/ ?>