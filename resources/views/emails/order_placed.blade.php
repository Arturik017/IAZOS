<p>Salut, {{ $order->first_name }} {{ $order->last_name }}!</p>

<p>Comanda ta a fost plasată cu succes.</p>

<p><strong>Număr comandă:</strong> #{{ $order->order_number ?? $order->id }}</p>
<p><strong>Total:</strong> {{ number_format($order->subtotal, 2) }} MDL</p>
<p><strong>Livrare:</strong> {{ $order->district }}, {{ $order->locality }} @if($order->postal_code) ({{ $order->postal_code }}) @endif</p>

<p><strong>Produse:</strong></p>
<ul>
@foreach($order->items as $it)
    <li>{{ $it->product_name }} × {{ $it->qty }} — {{ number_format($it->price * $it->qty, 2) }} MDL</li>
@endforeach
</ul>

<p>Îți mulțumim!</p>
