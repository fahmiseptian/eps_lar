<style>
    .product-item {
        position: relative;
        /* For positioning the heart icon */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        overflow: hidden;
    }

    .love-icon {
        position: absolute;
        top: 10px;
        right: 5px;
        width: 35px;
        height: 35px;
        background-color: white;
        box-shadow: 0 4px 4px #000000;
        /* Shadow biru lebih muda */
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .love-icon .material-icons {
        font-size: 20px;
        margin-left: 8px;
        color: red;
        transition: transform 0.3s ease;
    }

    .love-icon:hover .material-icons {
        transform: scale(1.2);
    }
</style>

<div class="transactions-container">
    <h3 class="transactions-title">Favorite Saya</h3>

    <section class="products">
        <div class="product-grid" id="productGrid">
            @foreach ($wishlists as $wish)
            @php
            $requiresBaseUrl = strpos($wish->image, 'http') === false;
            $pc_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$wish->image : $wish->image;
            @endphp
            <div class="product-item">
                <a href="{{ route('product.show', ['id' => $wish->id , 'token'=>$token]) }}" class="product-link">
                    <img src="{{ $pc_image }}" alt="Produk">
                    <p title="{{ $wish->name }}">{{ $wish->name }}</p>
                    <p>Rp {{ number_format($wish->harga_tayang, 0, ',', '.') }}</p>
                    <div class="product-info">
                        <small title="Nama Toko">{{ $wish->nama_pt }}</small>
                        <small>{{ $wish->count_sold }} terjual</small>
                    </div>
                    <!-- Love icon in the top-right corner -->
                    <div class="love-icon">
                        <span class="material-icons">favorite</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
</div>