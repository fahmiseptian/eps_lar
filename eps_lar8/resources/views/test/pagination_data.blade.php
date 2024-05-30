<!-- resources/views/products/pagination_data.blade.php -->
<div class="row">
    @foreach($products as $product)
        <div class="col-md-4">
            <div class="card">
                <img class="card-img-top" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ $product->description }}</p>
                    <p class="card-text">Price: {{ $product->price }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="d-flex justify-content-center">
    <div class="pagination-links">
        {{ $products->links() }}
    </div>
</div>
