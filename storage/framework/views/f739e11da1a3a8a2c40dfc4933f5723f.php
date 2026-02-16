<div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50">
        <div class="font-bold text-gray-900">Categorii</div>
        <div class="text-xs text-gray-500">Alege o categorie</div>
    </div>

    <div class="p-4">
        <?php if(empty($categories) || $categories->count() === 0): ?>
            <div class="text-sm text-gray-500">Nu există categorii încă.</div>
        <?php else: ?>
            <ul class="space-y-2">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e(route('category.show', $cat)); ?>"
                           class="block px-3 py-2 rounded-lg hover:bg-gray-100 text-gray-900 font-medium">
                            <?php echo e($cat->name); ?>

                        </a>

                        <?php if($cat->children && $cat->children->count()): ?>
                            <ul class="mt-1 ml-3 border-l pl-3 space-y-1">
                                <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('subcategory.show', $sub)); ?>"
                                           class="block px-3 py-1.5 rounded-lg hover:bg-gray-100 text-sm text-gray-700">
                                            <?php echo e($sub->name); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/shop/partials/sidebar.blade.php ENDPATH**/ ?>