@foreach($productsearch as $product)
<div class="product-item">
    @php
    $requiresBaseUrl = strpos($product->image300, 'http') === false;
    $image300 = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $product->image300 : $product->image300;
    @endphp
    <a href="{{ route('product.show', ['id' => $product->id ,'token' => $token]) }}" class="product-link">
        <img src="{{ $image300 }}" alt="{{ $product->name }}">
        <p title="{{ $product->name }}">{{ Str::limit($product->name, 30) }}</p>
        <p>Rp {{ number_format($product->hargaTayang, 0, ',', '.') }}</p>
        <div class="product-info">
            <small title="{{ $product->namaToko }}">{{ Str::limit($product->namaToko, 20) }}</small>
            <small>{{ $product->total_sold ?? 0 }} terjual</small>
            <small>{{ $product->province_name }}</small>
        </div>
    </a>
</div>
@endforeach