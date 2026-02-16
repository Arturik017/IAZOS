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
        <h2 class="text-2xl font-bold text-gray-900">
            Finalizează comanda
        </h2>
     <?php $__env->endSlot(); ?>

    
    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-6">

            
            
            
            <div class="lg:col-span-7 space-y-6">

                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Date livrare
                    </h3>

                    
                    <form
                        method="POST"
                        action="<?php echo e(route('checkout.store')); ?>"
                        class="mt-5 space-y-4"
                    >
                        <?php echo csrf_field(); ?>

                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Nume
                                </label>
                                <input
                                    name="first_name"
                                    value="<?php echo e(old('first_name')); ?>"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                >
                                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Prenume
                                </label>
                                <input
                                    name="last_name"
                                    value="<?php echo e(old('last_name')); ?>"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                >
                                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Număr de telefon
                            </label>
                            <input
                                name="phone"
                                value="<?php echo e(old('phone')); ?>"
                                placeholder="+373..."
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            >
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div
                            x-data="mdLocations(<?php echo \Illuminate\Support\Js::from($districts)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($localitiesMap)->toHtml() ?>)"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                        >
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Raion
                                </label>
                        
                                <select
                                    name="district"
                                    x-model="district"
                                    @change="onDistrictChange()"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                    required
                                >
                                    <option value="">Alege raionul...</option>
                                    <template x-for="d in districts" :key="d">
                                        <option :value="d" x-text="d"></option>
                                    </template>
                                </select>
                        
                                <?php $__errorArgs = ['district'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Oraș / Sat
                                </label>
                        
                                <input
                                    type="text"
                                    x-model="search"
                                    :disabled="!district"
                                    placeholder="Caută după litere..."
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200 disabled:bg-gray-100"
                                />
                        
                                <select
                                    name="locality"
                                    x-model="locality"
                                    :disabled="!district"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200 disabled:bg-gray-100"
                                    required
                                >
                                    <option value="">Alege localitatea...</option>
                                    <template x-for="l in filteredLocalities" :key="l">
                                        <option :value="l" x-text="l"></option>
                                    </template>
                                </select>
                        
                                <?php $__errorArgs = ['locality'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Stradă / Adresă
                            </label>
                            <input
                                name="street"
                                value="<?php echo e(old('street')); ?>"
                                placeholder="Ex: str. Ștefan cel Mare 10, ap. 5"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                                required
                            >
                            <?php $__errorArgs = ['street'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Cod poștal (opțional)
                            </label>
                            <input
                                name="postal_code"
                                value="<?php echo e(old('postal_code')); ?>"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            >
                            <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Comentariu (opțional)
                            </label>
                            <textarea
                                name="customer_note"
                                rows="3"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-gray-200"
                            ><?php echo e(old('customer_note')); ?></textarea>
                            <?php $__errorArgs = ['customer_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="flex items-start gap-3">
                            <input
                                id="accept_terms"
                                name="accept_terms"
                                type="checkbox"
                                value="1"
                                class="mt-1 rounded border-gray-300"
                                required
                            >
                            <label for="accept_terms" class="text-sm text-gray-700">
                                Sunt de acord cu
                                <a
                                    href="<?php echo e(route('terms')); ?>"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                >
                                    Termenii și condițiile
                                </a>
                                (inclusiv condițiile MAIB).
                            </label>
                        </div>
                        <?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="mt-1 text-sm text-red-600"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        
                        <button
                            type="submit"
                            class="w-full px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
                        >
                            Plasează comanda
                        </button>
                    </form>

                    
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <img src="<?php echo e(asset('images/payments/visa.svg')); ?>" alt="Visa" class="h-7">
                        <img src="<?php echo e(asset('images/payments/mastercard.svg')); ?>" alt="Mastercard" class="h-7">
                        <img src="<?php echo e(asset('images/payments/google-pay.svg')); ?>" alt="Google Pay" class="h-7">
                        <img src="<?php echo e(asset('images/payments/apple-pay.svg')); ?>" alt="Apple Pay" class="h-7">
                    </div>
                </div>
                
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

            </div>

            
            
            
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 lg:sticky lg:top-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Coșul tău
                    </h3>

                    <div class="mt-4 space-y-3 max-h-[420px] overflow-auto pr-1">
                        <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start justify-between gap-3 border-b pb-3">
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        <?php echo e($item['name']); ?>

                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Cantitate: <?php echo e($item['qty']); ?>

                                    </div>
                                </div>

                                <div class="font-bold text-gray-900 whitespace-nowrap">
                                    <?php echo e(number_format($item['price'] * $item['qty'], 2)); ?> MDL
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-4 flex justify-between text-lg font-extrabold">
                        <span>Total</span>
                        <span><?php echo e(number_format($subtotal, 2)); ?> MDL</span>
                    </div>

                    <div class="mt-3 text-xs text-gray-500">
                        * Livrare în Republica Moldova (fără Transnistria).
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    <script>
        function mdLocations(districts, localitiesMap) {
            return {
                districts: Array.isArray(districts) ? districts : [],
                localitiesMap: localitiesMap && typeof localitiesMap === 'object' ? localitiesMap : {},

                district: <?php echo \Illuminate\Support\Js::from(old('district', ''))->toHtml() ?>,
                locality: <?php echo \Illuminate\Support\Js::from(old('locality', ''))->toHtml() ?>,
                search: '',

                get localities() {
                    if (!this.district) return [];
                    return this.localitiesMap[this.district] || [];
                },

                get filteredLocalities() {
                    const q = (this.search || '').toLowerCase().trim();
                    if (!q) return this.localities;
                    return this.localities.filter(x =>
                        (x || '').toLowerCase().includes(q)
                    );
                },

                onDistrictChange() {
                    this.search = '';
                    this.locality = '';
                },
            }
        }
                document.addEventListener('DOMContentLoaded', function () {
            if (!window.grecaptcha) return;
        
            grecaptcha.ready(function () {
                grecaptcha.execute("<?php echo e(config('recaptcha.site_key')); ?>", {action: 'checkout'}).then(function (token) {
                    const el = document.getElementById('recaptcha_token');
                    if (el) el.value = token;
                });
            });
        });
    </script>

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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/checkout.blade.php ENDPATH**/ ?>