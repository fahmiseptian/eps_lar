<!-- resources/views/products/complete_cart_shop_data.blade.php -->
<div class="row">
    @foreach($completeCartShopData as $data)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $data->title }}</h5>
                    <p class="card-text">{{ $data->description }}</p>
                    <p class="card-text">{{ $data->price }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="d-flex justify-content-center">
    <div class="pagination-links complete-cart-pagination">
        {{ $completeCartShopData->links() }}
    </div>
</div>
