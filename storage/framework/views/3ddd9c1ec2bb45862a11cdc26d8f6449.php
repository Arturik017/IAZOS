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
            <h2 class="text-2xl font-bold text-gray-900">Detalii despre plată</h2>
            <a href="<?php echo e(route('orders.index')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                Înapoi la comenzi
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <?php
        $pd = $order->payment_details ?? [];
        if (is_string($pd)) { $pd = json_decode($pd, true) ?: []; }

        $isPaid = (($order->payment_status ?? '') === 'paid');

        $status = data_get($pd, 'result.status');
        $statusCode = data_get($pd, 'result.statusCode');

        $amount = data_get($pd, 'result.amount');
        $currency = data_get($pd, 'result.currency', 'MDL');

        $payId = data_get($pd, 'result.payId', $order->pay_id);
        $maibOrderId = data_get($pd, 'result.orderId');

        $displayAmount = $amount ? ($amount . ' ' . $currency) : (number_format($order->subtotal, 2) . ' MDL');
        $displayDate = $order->paid_at ? $order->paid_at->format('d.m.Y H:i') : '—';
        $merchant = config('app.name');
        $website = parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');
        $desc = 'Plata comanda #' . ($order->order_number ?? $order->id);
    ?>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 space-y-6">

            
            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center <?php echo e($isPaid ? 'bg-green-100' : 'bg-yellow-100'); ?>">
                            <span class="text-2xl"><?php echo e($isPaid ? '✅' : '⏳'); ?></span>
                        </div>

                        <div class="min-w-0">
                            <div class="text-xl font-bold text-gray-900">
                                <?php echo e($isPaid ? 'Plată efectuată cu succes' : 'Plata se procesează'); ?>

                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                <?php echo e($isPaid ? 'Confirmarea plății este salvată în sistem.' : 'Te rugăm să revii peste câteva momente.'); ?>

                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Comerciant</div>
                            <div class="font-semibold text-gray-900"><?php echo e($merchant); ?></div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Website</div>
                            <div class="font-semibold text-gray-900"><?php echo e($website); ?></div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Sumă</div>
                            <div class="font-semibold text-gray-900"><?php echo e($displayAmount); ?></div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="text-xs text-gray-500">Data achitării</div>
                            <div class="font-semibold text-gray-900"><?php echo e($displayDate); ?></div>
                        </div>
                    </div>

                    
                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <dl class="space-y-3 text-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <dt class="text-gray-500">ID tranzacție</dt>
                                <dd class="font-mono font-semibold text-gray-900 break-all"><?php echo e($payId ?: '—'); ?></dd>
                            </div>

                            <?php if($maibOrderId): ?>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <dt class="text-gray-500">Order ID (MAIB)</dt>
                                    <dd class="font-mono font-semibold text-gray-900 break-all"><?php echo e($maibOrderId); ?></dd>
                                </div>
                            <?php endif; ?>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <dt class="text-gray-500">Descriere</dt>
                                <dd class="font-semibold text-gray-900"><?php echo e($desc); ?></dd>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <dt class="text-gray-500">Status (MAIB)</dt>
                                <dd class="font-mono font-semibold text-gray-900"><?php echo e($status ?? '—'); ?> / <?php echo e($statusCode ?? '—'); ?></dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="<?php echo e(route('orders.index')); ?>"
                           class="inline-flex justify-center items-center px-6 py-3.5 rounded-xl bg-gray-900 text-white text-base font-semibold hover:bg-black">
                            Înapoi la comenzi
                        </a>
                    
                        <?php if($payId): ?>
                            <button type="button"
                                    onclick="navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from((string)$payId)->toHtml() ?>); this.innerText='Copiat ✅';"
                                    class="inline-flex justify-center items-center px-6 py-3.5 rounded-xl border border-gray-200 text-base font-semibold hover:bg-gray-50">
                                Copiază ID tranzacție
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="text-xs text-gray-500">
                Păstrează ID-ul tranzacției pentru suport: <span class="font-mono"><?php echo e($payId ?: '—'); ?></span>
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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/payment/receipt.blade.php ENDPATH**/ ?>