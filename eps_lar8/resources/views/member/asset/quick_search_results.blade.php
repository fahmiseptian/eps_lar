@if(isset($categories) && $categories !== null && $categories->count() > 0)
<h3>Kategori</h3>
<ul>
    @foreach($categories as $category)
    <li>{{ $category->name }}</li>
    @endforeach
</ul>
@endif

@if(isset($productsearch) && $productsearch !== null && $productsearch->count() > 0)
<h3>Produk</h3>
<ul>
    @foreach($productsearch as $product)
    @php
    $requiresBaseUrl = strpos($product->image50, 'http') === false;
    $image50 = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$product->image50 : $product->image50;
    @endphp
    <li> <a href=" {{ route('product.show', ['id' => $product->id]) }} " style="color: inherit; text-decoration: none;"> <img src="{{ $image50 }}"> {{ $product->name }} </a> </li>
    @endforeach
</ul>
@endif

@if(isset($stores) && $stores !== null && $stores->count() > 0)
<h3>Toko</h3>
<ul>
    @foreach($stores as $store)
    <li>{{ $store->name }}</li>
    @endforeach
</ul>
@endif

@if((!isset($categories) || $categories === null || $categories->count() == 0) &&
(!isset($productsearch) || $productsearch === null || $productsearch->count() == 0) &&
(!isset($stores) || $stores === null || $stores->count() == 0))
<p>Tidak ada hasil yang ditemukan.</p>
@endif