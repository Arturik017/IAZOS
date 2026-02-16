<h2>Plata confirmată ✅</h2>

<p>Salut <?php echo e($order->customer_name); ?>!</p>

<p>
Plata pentru comanda <strong>#<?php echo e($order->order_number ?? $order->id); ?></strong>
a fost confirmată cu succes.
</p>

<ul>
    <li><strong>Suma:</strong> <?php echo e(number_format($order->subtotal, 2)); ?> MDL</li>
    <li><strong>Pay ID:</strong> <?php echo e($order->pay_id); ?></li>
    <li><strong>Data plății:</strong> <?php echo e(optional($order->paid_at)->format('d.m.Y H:i')); ?></li>
</ul>

<p>Îți mulțumim pentru încredere!</p>
<?php /**PATH /home/u948789017/domains/iazos.com/public_html/resources/views/emails/payment_confirmed.blade.php ENDPATH**/ ?>