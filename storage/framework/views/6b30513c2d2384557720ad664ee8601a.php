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
                <h2 class="text-2xl font-bold text-gray-900">Coșul meu</h2>
                <p class="text-sm text-gray-500">
                    Verifică produsele înainte de checkout.
                </p>
            </div>

            <a href="<?php echo e(route('home')); ?>"
               class="text-sm font-semibold text-gray-700 hover:text-gray-900">
                ← Înapoi la magazin
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-6">

            
            <?php if(session('success')): ?>
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            
            <?php if(empty($cart)): ?>
                <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center shadow">
                    <div class="text-lg font-semibold text-gray-900">
                        Coșul este gol.
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        Adaugă produse din magazin.
                    </div>

                    <a href="<?php echo e(route('home')); ?>"
                       class="mt-5 inline-block rounded-xl bg-gray-900 px-6 py-3 font-semibold text-white hover:bg-black transition">
                        Mergi la produse
                    </a>
                </div>

            <?php else: ?>
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    
                    <div class="space-y-4 lg:col-span-2">
                        <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow">

                                
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <?php echo e($item['name']); ?>

                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Preț:
                                            <span class="font-semibold text-gray-900">
                                                <?php echo e(number_format($item['price'], 2)); ?> MDL
                                            </span>
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            Stoc disponibil: <?php echo e($item['stock']); ?>

                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Total produs</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            <?php echo e(number_format($item['price'] * $item['qty'], 2)); ?> MDL
                                        </p>
                                    </div>
                                </div>

                                
                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">

                                    
                                    <form method="POST"
                                          action="<?php echo e(route('cart.update', $item['id'])); ?>"
                                          class="flex items-center gap-2">
                                        <?php echo csrf_field(); ?>

                                        <label class="text-sm text-gray-600">
                                            Cantitate
                                        </label>

                                        <input
                                            type="number"
                                            name="qty"
                                            min="1"
                                            max="<?php echo e($item['stock']); ?>"
                                            value="<?php echo e($item['qty']); ?>"
                                            class="w-24 rounded-lg border-gray-300 focus:border-gray-400 focus:ring-gray-400"
                                        />

                                        <button
                                            class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black transition">
                                            Actualizează
                                        </button>
                                    </form>

                                    
                                    <form method="POST"
                                          action="<?php echo e(route('cart.remove', $item['id'])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button
                                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                                            Șterge
                                        </button>
                                    </form>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    
                    <div class="h-fit rounded-2xl border border-gray-100 bg-white p-6 shadow">
                        <h3 class="text-lg font-bold text-gray-900">
                            Sumar comandă
                        </h3>

                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold text-gray-900">
                                    <?php echo e(number_format($subtotal, 2)); ?> MDL
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Livrare</span>
                                <span class="font-semibold text-green-700">Inclus</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Vamă</span>
                                <span class="font-semibold text-green-700">Inclus</span>
                            </div>
                        </div>

                        <div class="my-4 border-t"></div>

                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700">Total</span>
                            <span class="text-2xl font-extrabold text-gray-900">
                                <?php echo e(number_format($subtotal, 2)); ?> MDL
                            </span>
                        </div>

                        
                        <a href="<?php echo e(auth()->check() ? route('checkout.index') : route('register')); ?>"
                           class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-gray-900 px-4 py-3 font-semibold text-white hover:bg-black transition">
                            Finalizează comanda
                        </a>

                        
                        <form method="POST"
                              action="<?php echo e(route('cart.clear')); ?>"
                              class="mt-3">
                            <?php echo csrf_field(); ?>
                            <button
                                class="w-full rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-900 hover:bg-gray-200 transition">
                                Golește coșul
                            </button>
                        </form>

                        <p class="mt-4 text-xs text-gray-400">
                            În pasul următor colectăm datele de livrare și confirmăm comanda.
                        </p>
                    </div>

                </div>
            <?php endif; ?>
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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/cart.blade.php ENDPATH**/ ?>