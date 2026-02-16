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
            <h2 class="text-2xl font-bold text-gray-900">
                Comanda #<?php echo e($order->order_number ?? $order->id); ?>

            </h2>
            <a href="<?php echo e(route('admin.orders.index')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                ← Înapoi la comenzi
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 space-y-6">

            <?php if(session('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Client</div>
                        <div class="font-semibold text-gray-900">
                            <?php echo e($order->first_name); ?> <?php echo e($order->last_name); ?>

                        </div>
                        <div class="text-sm text-gray-600"><?php echo e($order->phone); ?></div>
                    </div>

                    <div>
                        <div class="font-semibold text-gray-900">
                            <?php echo e($order->district); ?>, <?php echo e($order->locality); ?>, <?php echo e($order->street); ?>

                        </div>

                        <?php if($order->postal_code): ?>
                            <div class="text-sm text-gray-600">
                                Cod poștal: <?php echo e($order->postal_code); ?>

                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="text-2xl font-extrabold text-gray-900">
                            <?php echo e(number_format($order->subtotal, 2)); ?> MDL
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <div class="font-semibold text-gray-900"><?php echo e($order->status); ?></div>
                    </div>
                </div>

                <?php if($order->customer_note): ?>
                    <div class="mt-4 p-4 rounded-xl bg-gray-50 border">
                        <div class="text-sm font-semibold text-gray-900">Notă client</div>
                        <div class="text-sm text-gray-700 mt-1">
                            <?php echo e($order->customer_note); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900">Produse</h3>

                <div class="mt-4 divide-y">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="py-3 flex justify-between">
                            <div>
                                <div class="font-semibold"><?php echo e($it->product_name); ?></div>
                                <div class="text-sm text-gray-500">Cantitate: <?php echo e($it->qty); ?></div>
                            </div>
                            <div class="font-bold">
                                <?php echo e(number_format($it->price * $it->qty, 2)); ?> MDL
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <form method="POST"
                      action="<?php echo e(route('admin.orders.status', $order)); ?>"
                      class="flex flex-col sm:flex-row gap-3 items-center">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>

                    <label class="text-sm font-semibold">Schimbă status:</label>

                    <select name="status" class="rounded-lg border-gray-300 shadow-sm">
                        <?php $__currentLoopData = ['new','confirmed','processing','shipped','delivered','canceled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st); ?>" <?php if($order->status === $st): echo 'selected'; endif; ?>>
                                <?php echo e($st); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <button class="px-6 py-2 rounded-lg bg-gray-900 text-white font-semibold">
                        Salvează
                    </button>
                </form>
            </div>

            
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Plată</div>
                <div class="font-semibold"><?php echo e($order->payment_status); ?></div>

                <?php if($order->pay_id): ?>
                    <div class="text-sm text-gray-600">
                        Pay ID: <span class="font-mono"><?php echo e($order->pay_id); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if($order->pay_id): ?>
                    <form method="POST" action="<?php echo e(route('admin.orders.maib_refresh', $order)); ?>" class="mt-3">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 text-sm font-semibold">
                            Actualizează status MAIB
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if($order->refund_status): ?>
                    <div class="mt-2 text-sm text-gray-600">
                        Refund status: <span class="font-semibold"><?php echo e($order->refund_status); ?></span>
                        <?php if($order->refunded_at): ?> • <?php echo e($order->refunded_at->format('d.m.Y H:i')); ?> <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>

            
            <?php if($order->payment_status === 'paid'): ?>
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Refund (MAIB)</h3>

                    <form method="POST"
                          action="<?php echo e(route('admin.orders.refund', $order)); ?>"
                          class="mt-4 flex flex-col sm:flex-row gap-3 items-end">
                        <?php echo csrf_field(); ?>

                        <div class="w-full sm:w-48">
                            <label class="block text-sm font-medium">Sumă (MDL)</label>
                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   value="<?php echo e(number_format($order->subtotal, 2, '.', '')); ?>"
                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm">
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium">Motiv (opțional)</label>
                            <input name="reason"
                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm"
                                   placeholder="Ex: client a anulat comanda">
                        </div>

                        <button type="submit"
                                onclick="return confirm('Sigur faci refund?')"
                                class="px-6 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">
                            Refund
                        </button>
                    </form>
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
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/admin/orders/show.blade.php ENDPATH**/ ?>