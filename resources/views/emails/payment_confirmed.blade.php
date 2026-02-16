<h2>Plata confirmată ✅</h2>

<p>Salut {{ $order->customer_name }}!</p>

<p>
Plata pentru comanda <strong>#{{ $order->order_number ?? $order->id }}</strong>
a fost confirmată cu succes.
</p>

<ul>
    <li><strong>Suma:</strong> {{ number_format($order->subtotal, 2) }} MDL</li>
    <li><strong>Pay ID:</strong> {{ $order->pay_id }}</li>
    <li><strong>Data plății:</strong> {{ optional($order->paid_at)->format('d.m.Y H:i') }}</li>
</ul>

<p>Îți mulțumim pentru încredere!</p>
