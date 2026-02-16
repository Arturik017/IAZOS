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
                <h2 class="text-2xl font-bold text-gray-900"><?php echo e($category->name); ?></h2>
                <p class="text-sm text-gray-500">Produse în această categorie</p>
            </div>

            <a href="<?php echo e(route('home')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                ← Înapoi la Home
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
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





                <div class="flex-1 space-y-6">

                    
                    <div x-data="{ openFilters: false }" class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                        
                        <button
                            type="button"
                            @click="openFilters = !openFilters"
                            class="w-full flex items-center justify-between px-6 py-5"
                        >
                            <div class="text-left py-2">
                                <div class="font-semibold text-gray-900">Filtre & sortare</div>
                                <div class="text-xs text-gray-500">Apasă ca să deschizi / închizi filtrele</div>
                            </div>
                            <div class="text-gray-500 text-2xl leading-none" x-text="openFilters ? '−' : '+'"></div>
                        </button>
                    
                        
                        <div
                            x-ref="panel"
                            x-show="openFilters"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-[800px]"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 max-h-[800px]"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="border-t border-gray-100 overflow-hidden"
                        >
                            <div class="px-6 pt-5 pb-10">
                                <form method="GET">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                                        
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="checkbox" name="promo" value="1"
                                                   class="rounded border-gray-300"
                                                   <?php echo e(request()->boolean('promo') ? 'checked' : ''); ?>>
                                            Doar promoții
                                        </label>
                    
                                        
                                        <label class="flex items-center gap-2 text-sm text-gray-700">
                                            <input type="checkbox" name="in_stock" value="1"
                                                   class="rounded border-gray-300"
                                                   <?php echo e(request()->boolean('in_stock') ? 'checked' : ''); ?>>
                                            Doar în stoc
                                        </label>
                    
                                        
                                        <div>
                                            <label class="block text-xs text-gray-500">Preț minim</label>
                                            <input type="number" step="0.01" name="min_price"
                                                   value="<?php echo e(request('min_price')); ?>"
                                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                        </div>
                    
                                        
                                        <div>
                                            <label class="block text-xs text-gray-500">Preț maxim</label>
                                            <input type="number" step="0.01" name="max_price"
                                                   value="<?php echo e(request('max_price')); ?>"
                                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                        </div>
                    
                                        
                                        <div class="sm:col-span-2 lg:col-span-1">
                                            <label class="block text-xs text-gray-500">Sortare</label>
                                            <select name="sort" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                                                <option value="new" <?php echo e(request('sort','new')==='new' ? 'selected' : ''); ?>>Cele mai noi</option>
                                                <option value="price_asc" <?php echo e(request('sort')==='price_asc' ? 'selected' : ''); ?>>Preț crescător</option>
                                                <option value="price_desc" <?php echo e(request('sort')==='price_desc' ? 'selected' : ''); ?>>Preț descrescător</option>
                                            </select>
                                        </div>
                    
                                        
                                        <div class="sm:col-span-2 lg:col-span-3 flex flex-wrap gap-3 items-end">
                                            <button type="submit"
                                                    class="px-6 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition">
                                                Aplică filtre
                                            </button>
                    
                                            <a href="<?php echo e(url()->current()); ?>"
                                               class="px-6 py-3 rounded-xl border border-gray-200 bg-white text-gray-900 font-semibold hover:bg-gray-50 transition">
                                                Resetează
                                            </a>
                                        </div>
                                    </div>
                    
                                    
                                    <div class="h-3"></div>
                                </form>
                            </div>
                        </div>
                    </div>



                    
                    <?php if($products->count() === 0): ?>
                        <div class="bg-white p-10 rounded-2xl shadow text-center border border-gray-100">
                            <div class="text-gray-900 font-semibold">Nu există produse pentru filtrele selectate.</div>
                            <div class="text-gray-500 text-sm mt-1">Încearcă alte filtre.</div>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('shop.partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    
                    
                    <div class="mt-8">
                        <?php echo e($products->links()); ?>

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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/category.blade.php ENDPATH**/ ?>