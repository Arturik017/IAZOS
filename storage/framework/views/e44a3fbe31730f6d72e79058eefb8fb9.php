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
                <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
                <p class="text-sm text-gray-500">Gestionează produse, categorii și bannere.</p>
            </div>

            <a href="<?php echo e(route('home')); ?>"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                Vezi site
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <?php if(session('success')): ?>
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Produse</div>
                            <div class="text-xl font-bold text-gray-900">Administrare</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700 font-bold">
                            P
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Adaugă, editează, șterge produse, poze, promoții.
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a href="<?php echo e(route('admin.products.index')); ?>"
                           class="w-full text-center px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black">
                            Lista produse
                        </a>
                        <a href="<?php echo e(route('admin.products.create')); ?>"
                           class="w-full text-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Adaugă
                        </a>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Categorii</div>
                            <div class="text-xl font-bold text-gray-900">Structură</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-700 font-bold">
                            C
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Creează categorii și subcategorii pentru sidebar.
                    </p>

                    <div class="mt-5">
                        <a href="<?php echo e(route('admin.categories.index')); ?>"
                           class="block w-full text-center px-4 py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700">
                            Gestionare categorii
                        </a>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Bannere</div>
                            <div class="text-xl font-bold text-gray-900">Carusel</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-700 font-bold">
                            B
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Adaugă bannere pentru carusel (poză, titlu, subtitlu).
                    </p>

                    <div class="mt-5 flex gap-2">
                        <a href="<?php echo e(route('admin.banners.index')); ?>"
                           class="w-full text-center px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black">
                            Lista bannere
                        </a>
                        <a href="<?php echo e(route('admin.banners.create')); ?>"
                           class="w-full text-center px-4 py-2 rounded-lg bg-orange-600 text-white font-semibold hover:bg-orange-700">
                            Adaugă
                        </a>
                    </div>
                </div>

                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-gray-500">Comenzi</div>
                            <div class="text-xl font-bold text-gray-900">Vânzări</div>
                        </div>
                        <div class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center text-green-700 font-bold">
                            O
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">
                        Vezi și gestionează comenzile clienților.
                    </p>

                    <div class="mt-5">
                        <a href="<?php echo e(route('admin.orders.index')); ?>"
                        class="block w-full text-center px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
                            Lista comenzi
                        </a>
                    </div>
                </div>


            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">Sfat</div>
                        <div class="text-sm text-gray-600">
                            Dacă ai schimbat DB / migrații și ceva nu apare, rulează:
                            <span class="font-mono">php artisan optimize:clear</span>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">
                            Logout
                        </button>
                    </form>
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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/admin/products/dashboard.blade.php ENDPATH**/ ?>