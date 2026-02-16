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
            <h2 class="text-2xl font-bold text-gray-900">Comenzi (Admin)</h2>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                Înapoi la Dashboard
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 space-y-8">

            <?php if(session('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php
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

                $allowed = ['new','confirmed','processing','shipped','delivered','canceled'];
            ?>

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
                            <div class="rounded-2xl border border-gray-100 p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">
                                            Comanda #<?php echo e($order->order_number ?? $order->id); ?>

                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo e(optional($order->created_at)->format('d.m.Y H:i')); ?>

                                            • <?php echo e($order->first_name ?? ''); ?> <?php echo e($order->last_name ?? ''); ?>

                                            • <?php echo e($order->phone ?? ''); ?>

                                        </div>
                                        <div class="mt-1 text-sm text-gray-600">
                                            <span class="font-semibold">Adresă:</span>
                                            <?php echo e($order->district ?? ''); ?>, <?php echo e($order->locality ?? ''); ?>

                                            <?php if(!empty($order->postal_code)): ?> (<?php echo e($order->postal_code); ?>) <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo e($badge[$order->status] ?? $badge['unknown']); ?>">
                                            <?php echo e($pretty[$order->status] ?? $order->status); ?>

                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo e($payBadge[$order->payment_status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo e($payPretty[$order->payment_status] ?? ($order->payment_status ?? '—')); ?>

                                        </span>

                                        <?php if($order->pay_id): ?>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Pay ID: <span class="font-mono"><?php echo e($order->pay_id); ?></span>
                                            </div>
                                        <?php endif; ?>


                                        <div class="text-lg font-extrabold text-gray-900">
                                            <?php echo e(number_format($order->subtotal, 2)); ?> MDL
                                        </div>

                                        <a href="<?php echo e(route('admin.orders.show', $order)); ?>"
                                           class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm font-semibold">
                                            Detalii
                                        </a>
                                    </div>
                                </div>

                                
                                <div class="mt-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900 mb-2">Produse:</div>
                                    <div class="space-y-1">
                                        <?php $__currentLoopData = $order->items->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex justify-between">
                                                <div><?php echo e($it->product_name); ?> <span class="text-gray-400">× <?php echo e($it->qty); ?></span></div>
                                                <div class="font-semibold"><?php echo e(number_format($it->price * $it->qty, 2)); ?> MDL</div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($order->items->count() > 4): ?>
                                            <div class="text-gray-400">+ încă <?php echo e($order->items->count() - 4); ?> produse…</div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <form method="POST" action="<?php echo e(route('admin.orders.status', $order)); ?>" class="flex flex-col sm:flex-row gap-3 sm:items-center">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <label class="text-sm font-semibold text-gray-900">Schimbă status:</label>

                                        <select name="status" class="rounded-lg border-gray-300 shadow-sm">
                                            <?php $__currentLoopData = $allowed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($st); ?>" <?php if($order->status === $st): echo 'selected'; endif; ?>>
                                                    <?php echo e($pretty[$st] ?? $st); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                        <button type="submit"
                                                class="px-5 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                                            Salvează
                                        </button>

                                        <div class="text-xs text-gray-500">
                                            După salvare, comanda va apărea automat în secțiunea statusului nou.
                                        </div>
                                    </form>
                                </div>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/admin/orders/index.blade.php ENDPATH**/ ?>