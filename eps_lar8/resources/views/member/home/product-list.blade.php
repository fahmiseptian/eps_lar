@foreach ($products as $product)
    <div class="product-item">
        <a href="{{ route('product.show', ['id' => $product->id]) }}" class="product-link">
            <img src="{{ $product->artwork_url_md[0] }}" alt="Produk">
            <p title="{{ $product->name }}">{{ substr($product->name, 0, 20) }}...</p>
            <p>Rp {{ number_format($product->hargaTayang, 0, ',', '.') }}</p>
            <div class="product-info">
                <small title="{{$product->namaToko}}">{{ substr($product->namaToko, 0, 6) }}...</small>
                <small>{{ $product->count_sold }} terjual</small>
                <small>{{ $product->province_name }}</small>
            </div>
        </a>
    </div>
@endforeach
