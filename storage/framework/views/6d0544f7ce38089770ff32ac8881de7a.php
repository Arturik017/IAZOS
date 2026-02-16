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
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-12 gap-6">

                
                <aside class="col-span-12 lg:col-span-3">
                    <div class="lg:sticky lg:top-6">
                        <?php echo $__env->make('shop.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </aside>

                
                <main class="col-span-12 lg:col-span-9">
                    <div class="bg-white rounded-2xl shadow border border-gray-100 p-5 sm:p-6">
                        <h1 class="text-xl font-bold text-gray-900">
                            Rezultate căutare
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Căutare: <span class="font-semibold text-gray-900"><?php echo e($q ?: '—'); ?></span>
                            • Găsite: <span class="font-semibold text-gray-900"><?php echo e($products->total()); ?></span>
                        </p>
                    </div>

                    <div class="mt-6">
                        <?php if($products->isEmpty()): ?>
                            <div class="bg-white p-10 rounded-2xl shadow text-center border border-gray-100">
                                <div class="text-gray-900 font-semibold">Nu am găsit produse.</div>
                                <div class="text-gray-500 text-sm mt-1">Încearcă alte cuvinte.</div>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('product.show', $product)); ?>"
                                       class="block bg-white rounded-2xl shadow border border-gray-100 overflow-hidden hover:shadow-xl transition">
                                        <?php if($product->image): ?>
                                            <img src="<?php echo e(asset('storage/'.$product->image)); ?>"
                                                 class="h-44 w-full object-cover" alt="<?php echo e($product->name); ?>">
                                        <?php else: ?>
                                            <div class="h-44 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                                Fără imagine
                                            </div>
                                        <?php endif; ?>

                                        <div class="p-5">
                                            <div class="font-semibold text-gray-900"><?php echo e($product->name); ?></div>

                                            <div class="mt-2 flex items-end justify-between">
                                                <div class="text-xl font-extrabold text-gray-900">
                                                    <?php echo e(number_format($product->final_price, 2)); ?> MDL
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Stoc: <span class="font-semibold text-gray-900"><?php echo e($product->stock); ?></span>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <form method="POST" action="<?php echo e(route('cart.add', $product)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit"
                                                        class="w-full px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                                                        Adaugă în coș
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="mt-6">
                                <?php echo e($products->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </main>

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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/search.blade.php ENDPATH**/ ?>