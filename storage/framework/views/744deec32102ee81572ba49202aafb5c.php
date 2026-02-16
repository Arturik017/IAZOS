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
    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 sm:p-10">
                <h1 class="text-3xl font-extrabold text-gray-900">Despre noi</h1>

                <p class="mt-4 text-gray-700 leading-relaxed">
                    Suntem un magazin online din Republica Moldova, specializat în electronice și accesorii.
                    Selectăm produse la prețuri bune și afișăm un preț final clar — cu vama și livrarea incluse.
                </p>

                <div class="mt-6 grid sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-xl border border-gray-100 bg-gray-50">
                        <div class="font-semibold text-gray-900">Preț fix</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Clientul achită o singură dată și primește comanda, fără costuri ascunse.
                        </div>
                    </div>

                    <div class="p-5 rounded-xl border border-gray-100 bg-gray-50">
                        <div class="font-semibold text-gray-900">Livrare în Moldova</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Livrare pe teritoriul Republicii Moldova (fără Transnistria).
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-900">Contact</h2>
                    <p class="text-gray-700 mt-2">
                        Email: <span class="font-semibold">support@site.md</span><br>
                        Telefon: <span class="font-semibold">+373 xx xxx xxx</span>
                    </p>

                    <p class="text-xs text-gray-500 mt-4">
                        (Înlocuiește datele de contact cu cele reale când ești gata.)
                    </p>
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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/about.blade.php ENDPATH**/ ?>