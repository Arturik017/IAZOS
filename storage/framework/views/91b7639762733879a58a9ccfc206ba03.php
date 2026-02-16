<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900"><?php echo e($product->name); ?></h2>
                <p class="text-sm text-gray-500">Detalii produs</p>
            </div>

            <a href="<?php echo e(url()->previous()); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                ← Înapoi
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">

                <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-6">

                
                <aside class="w-full lg:w-72" x-data="{ openCats: false }">
                
                    
                    <div class="lg:hidden">
                        <button
                            type="button"
                            @click="openCats = !openCats"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-2xl bg-white shadow border border-gray-100"
                        >
                            <span class="font-semibold text-gray-900">Categorii</span>
                
                            
                            <!--<span class="text-gray-500" x-text="openCats ? '▲' : '▼'"></span>-->
                        </button>
                    </div>
                
                    
                    <div
                        class="mt-3 lg:mt-0 lg:sticky lg:top-6 lg:block"
                        :class="openCats ? 'block' : 'hidden'"
                    >
                        <?php echo $__env->make('shop.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                
                </aside>

                <div class="flex-1 space-y-8">

                    
                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2">
                            <div class="bg-gray-50">
                                <?php if($product->image): ?>
                                    <img src="<?php echo e(asset('storage/'.$product->image)); ?>"
                                         class="w-full h-[320px] sm:h-[420px] object-cover"
                                         alt="<?php echo e($product->name); ?>">
                                <?php else: ?>
                                    <div class="w-full h-[320px] sm:h-[420px] flex items-center justify-center text-gray-400">
                                        Fără imagine
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-6 sm:p-8">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-3xl font-extrabold text-gray-900">
                                        <?php echo e(number_format($product->final_price, 2)); ?> MDL
                                    </div>

                                    <?php if((int)$product->stock > 0): ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            În stoc
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Stoc epuizat
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-3 text-sm text-gray-600">
                                    Stoc: <span class="font-semibold text-gray-900"><?php echo e($product->stock); ?></span>
                                </div>

                                <?php if(!empty($product->description)): ?>
                                    <div class="mt-5">
                                        <div class="text-sm font-semibold text-gray-900">Descriere</div>
                                        <div class="mt-2 text-sm text-gray-600 leading-relaxed">
                                            <?php echo e($product->description); ?>

                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-6 flex gap-3">
                                    <form method="POST" action="<?php echo e(route('cart.add', $product)); ?>" class="w-full">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                                                <?php if((int)$product->stock <= 0 || (int)$product->status !== 1): echo 'disabled'; endif; ?>>
                                            Adaugă în coș
                                        </button>
                                    </form>

                                    <form method="POST" action="<?php echo e(route('cart.buy', $product)); ?>" class="w-full">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition disabled:opacity-50"
                                                <?php if((int)$product->stock <= 0 || (int)$product->status !== 1): echo 'disabled'; endif; ?>>
                                            Cumpără acum
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-4 text-xs text-gray-400">
                                    Prețurile includ vama și livrarea în Moldova.
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Produse similare</h3>
                        <p class="text-sm text-gray-500">Din aceeași categorie/subcategorie.</p>

                        <?php if($similarProducts->isEmpty()): ?>
                            <div class="mt-5 p-10 text-center rounded-xl border border-dashed border-gray-200 text-gray-600">
                                Nu există produse similare momentan.
                            </div>
                        <?php else: ?>
                            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php $__currentLoopData = $similarProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('shop.partials.product-card', ['product' => $p], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/product.blade.php ENDPATH**/ ?>