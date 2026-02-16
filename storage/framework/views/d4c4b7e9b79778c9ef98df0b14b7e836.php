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
            <h2 class="text-2xl font-bold text-gray-900">Comenzile mele</h2>
            <a href="<?php echo e(route('home')); ?>" class="text-sm text-gray-600 hover:text-gray-900">← Înapoi la Home</a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 space-y-8">

            <?php
                $pretty = [
                    'new' => 'Noi',
                    'confirmed' => 'Confirmate',
                    'processing' => 'În procesare',
                    'shipped' => 'Expediate',
                    'delivered' => 'Livrate',
                    'canceled' => 'Anulate',
                    'unknown' => 'Altele',
                ];

                $badge = [
                    'new' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-indigo-100 text-indigo-800',
                    'shipped' => 'bg-purple-100 text-purple-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'canceled' => 'bg-red-100 text-red-800',
                    'unknown' => 'bg-gray-100 text-gray-800',
                ];

                $payPretty = [
                    'unpaid' => 'Neachitat',
                    'pending' => 'În procesare',
                    'paid' => 'Achitat',
                    'failed' => 'Eșuat',
                    'refunded' => 'Returnat',
                ];

                $payBadge = [
                    'unpaid' => 'bg-gray-100 text-gray-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'paid' => 'bg-green-100 text-green-800',
                    'failed' => 'bg-red-100 text-red-800',
                    'refunded' => 'bg-indigo-100 text-indigo-800',
                ];

                $payDot = [
                    'unpaid' => 'bg-gray-400',
                    'pending' => 'bg-yellow-500',
                    'paid' => 'bg-green-600',
                    'failed' => 'bg-red-600',
                    'refunded' => 'bg-indigo-600',
                ];
            ?>

            <?php if($grouped->isEmpty()): ?>
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-10 text-center">
                    <div class="text-gray-900 font-semibold">Nu ai încă comenzi.</div>
                    <div class="text-gray-500 text-sm mt-1">După ce plasezi o comandă, o vei vedea aici.</div>
                </div>
            <?php else: ?>

                <?php $__currentLoopData = $statusOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $list = $grouped->get($status, collect()); ?>
                    <?php if($list->isEmpty()): ?> <?php continue; ?> <?php endif; ?>

                    <section class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <?php echo e($pretty[$status] ?? strtoupper($status)); ?>

                            </h3>
                            <span class="text-sm text-gray-500"><?php echo e($list->count()); ?> comenzi</span>
                        </div>

                        <div class="mt-5 space-y-4">
                            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $payStatus = $order->payment_status ?? 'unpaid';

                                    $pd = $order->payment_details ?? [];
                                    if (is_string($pd)) { $pd = json_decode($pd, true) ?: []; }

                                    $txId = data_get($pd, 'result.payId', $order->pay_id);
                                    $isPaid = ($payStatus === 'paid');
                                ?>

                                <div class="rounded-2xl border border-gray-100 p-5 hover:border-gray-200 transition">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <div class="font-semibold text-gray-900">
                                                    Comanda #<?php echo e($order->order_number ?? $order->id); ?>

                                                </div>

                                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold <?php echo e($badge[$order->status] ?? $badge['unknown']); ?>">
                                                    <?php echo e($pretty[$order->status] ?? $order->status); ?>

                                                </span>

                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold <?php echo e($payBadge[$payStatus] ?? 'bg-gray-100 text-gray-800'); ?>">
                                                    <span class="inline-block w-3 h-3 rounded-full <?php echo e($payDot[$payStatus] ?? 'bg-gray-400'); ?>"></span>
                                                    <?php echo e($payPretty[$payStatus] ?? $payStatus); ?>

                                                </span>
                                            </div>

                                            <div class="mt-1 text-sm text-gray-500">
                                                Plasată: <?php echo e(optional($order->created_at)->format('d.m.Y H:i')); ?>

                                                <?php if($isPaid && $order->paid_at): ?>
                                                    • Achitată: <?php echo e($order->paid_at->format('d.m.Y H:i')); ?>

                                                <?php endif; ?>
                                            </div>

                                            <?php if($txId): ?>
                                                <div class="mt-2 text-xs text-gray-500">
                                                    ID tranzacție: <span class="font-mono break-all text-gray-700"><?php echo e($txId); ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <div class="mt-4 text-sm text-gray-700">
                                                <div class="font-semibold text-gray-900 mb-2">Produse</div>
                                                <div class="space-y-1">
                                                    <?php $__currentLoopData = $order->items->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="flex justify-between">
                                                            <div class="truncate pr-4">
                                                                <?php echo e($it->product_name); ?>

                                                                <span class="text-gray-400">× <?php echo e($it->qty); ?></span>
                                                            </div>
                                                            <div class="font-semibold whitespace-nowrap">
                                                                <?php echo e(number_format($it->price * $it->qty, 2)); ?> MDL
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                    <?php if($order->items->count() > 3): ?>
                                                        <div class="text-gray-400 text-xs">
                                                            + încă <?php echo e($order->items->count() - 3); ?> produse…
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <?php if(!empty($order->customer_note)): ?>
                                                <div class="mt-4 text-sm text-gray-600">
                                                    <span class="font-semibold text-gray-900">Notă:</span>
                                                    <?php echo e($order->customer_note); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-col items-start lg:items-end gap-3">
                                            <div class="text-lg font-extrabold text-gray-900">
                                                <?php echo e(number_format($order->subtotal, 2)); ?> MDL
                                            </div>

                                            <?php if(!empty($order->pay_id)): ?>
                                                <a href="<?php echo e(route('pay.maib.receipt', ['payId' => $order->pay_id])); ?>"
                                                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-900 text-white text-base font-semibold hover:bg-black">
                                                    Detalii plată
                                                    <span aria-hidden="true">→</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/orders.blade.php ENDPATH**/ ?>