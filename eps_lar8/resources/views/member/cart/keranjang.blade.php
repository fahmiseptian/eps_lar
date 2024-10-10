<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')
    <!-- Main Content -->
    <main>
        <section class="cart-member">
            <div class="detail-cart">
                <div class="cart-header">
                    <div class="select-all">
                        <span class="material-icons google-icon">
                            check_box_outline_blank
                        </span>
                        &nbsp;
                        <span>Pilih Semua</span>
                    </div>
                    <b>Hapus Semua</b>
                </div>

                @foreach ($cart->detail as $detail)
                <div class="cart-detail-seller">
                    <span class="material-icons google-icon">
                        <a class="select-all-seller" data-id_cart="{{ $cart->id }}" data-id_seller="{{ $detail->id_shop }}">
                            <span id="icon-seller-{{ $detail->id_shop }}" class="material-icons {{ $detail->products->every(fn($product) => $product->is_selected == 'Y') ? 'check-box' : 'check-box-outline-blank' }}">
                                {{ $detail->products->every(fn($product) => $product->is_selected == 'Y') ? 'check_box' : 'check_box_outline_blank' }}
                            </span>
                        </a>
                    </span>
                    <b class="nama-seller-cart">{{ $detail->nama_seller }}</b>
                    <hr>
                    @foreach ($detail->products as $product)
                    <div class="detail-product-cart" data-shop-id="{{ $detail->id_shop }}">
                        <div class="product-info">
                            <a class="updateIsSelectProduct" data-id_cart="{{ $cart->id }}" data-id_cst="{{ $product->id_cst }}">
                                <span id="icon-{{ $product->id_cst }}" class="material-icons {{ $product->is_selected == 'Y' ? 'check-box' : 'check-box-outline-blank' }}">
                                    {{ $product->is_selected == 'Y' ? 'check_box' : 'check_box_outline_blank' }}
                                </span>
                            </a>
                            @php
                            $requiresBaseUrl = strpos($product->gambar_product, 'http') === false;
                            $image = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$product->gambar_product :$product->gambar_product;
                            @endphp
                            <img src="{{$image}}" alt="product" class="product-image-cart">
                            <div class="produk">
                                <p style="margin-left: 10px; font-size: 14px; margin-bottom:-1px">{{ $product->nama_product }}</p>
                                <p style="margin-left: 10px; font-size: 12px; color: gray;">Rp {{ number_format($product->price, 0, ',', '.') }}</p> <!-- Harga satuan -->
                            </div>
                        </div>
                        <b class="product-price-cart" id="price-{{ $product->id_cst }}">Rp {{ number_format($product->price * $product->qty, 0, ',', '.') }}</b> <!-- Harga total -->
                    </div>
                    <hr style="margin-left: 20px;">
                    <div class="button-aksi-cart">
                        <div class="left-actions">
                            <button class="btn-nego" onclick="openNegoModal()" data-id_user="{{$id_user}}">Nego</button>
                            <button class="btn-hapus" id="deleteCart" data-idtemp="{{$product->id_cst }}" data-idshop="{{$detail->id_shop}}">Hapus</button>
                        </div>
                        <div class="qty-produk-cart">
                            <div class="qty-cart-produk">
                                <button class="btn-qty btn-kurang" data-id_cst="{{ $product->id_cst }}" data-id="{{ $product->id }}">-</button>
                                <input type="text" data-max="{{ $product->stock }}" id="qty-product-cart-{{ $product->id }}" data-id_cst="{{ $product->id_cst }}" data-id_cs="{{ $detail->id_cs }}" data-id="{{ $product->id }}" value="{{ $product->qty }}" class="input-qty">
                                <button class="btn-qty btn-tambah" data-id_cst="{{ $product->id_cst }}" data-id="{{ $product->id }}">+</button>
                            </div>
                            <p id="empty-{{ $product->id }}" style="display: none; color: red;">Minimal 1 Produk</p>
                        </div>
                    </div>
                    <hr style="margin-left: 20px;">
                    @endforeach
                </div>
                @endforeach
            </div>
            <div class="sub-total-cart">
                <b style="font-size: 18px; margin-bottom: 10px;" align="left">Ringkasan Belanja</b>
                <hr>
                <div class="sub-total-cart-content">
                    <p>Total Pesanan</p>
                    <p id="totalqty">{{ $cart->qty }} Product</p>
                </div>
                <div class="sub-total-cart-content">
                    <p>Total</p>
                    <b id="total-cart">Rp {{ number_format($cart->sumprice, 0, ',', '.') }}</b>
                </div>
                <hr>
                <a href="{{route('checkout' , ['token'=> $token])}}" class="btn-checkout">Checkout</a>
            </div>
        </section>
    </main>

    <div id="negoModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="head-modal">
                <h2>Nego Harga</h2>
                <span class="close" onclick="closeNegoModal()">&times;</span>
            </div>
            <div class="quantity-input-nego">
                <button type="button" class="quantity-btn-nego minus" onclick="updateQuantityNego(-1)">-</button>
                <input type="number" id="quantity-nego" name="quantity-nego" min="1" max="{{$product->stock}}" value="1" step="1">
                <button type="button" class="quantity-btn-nego plus" onclick="updateQuantityNego(1)">+</button>
            </div>
            <p>Stok tersisa: <span id="stock-remaining">{{$product->stock}}</span></p>

            <label for="initial-price">Harga Awal Satuan:</label>
            <input type="text" id="initial-price" value="Rp {{ number_format($product->price, 0, ',', '.') }}" readonly>

            <label for="nego-price">Harga Nego Satuan:</label>
            <input type="text" data-id_produk="{{ $product->id_product }}" data-price="{{ $product->price }}" id="nego-price">

            <label for="total-price">Harga Nego Total:</label>
            <input type="text" id="total-price-nego" readonly>

            <label for="note-nego">Catatan:</label>
            <textarea id="note-nego"></textarea>

            <button style="margin-top: 40px;" onclick="submitNego()" class="nego-btn">Kirim Nego</button>
        </div>
    </div>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    <script>
        function openNegoModal() {
            var id_user = $(".btn-nego").data("id_user");

            if (id_user == null || id_user == "") {
                Swal.fire({
                    title: "Perhatian",
                    text: "Harap login terlebih dahulu untuk menambahkan produk ke keranjang.",
                    icon: "warning",
                    confirmButtonText: "OK",
                    showCancelButton: true,
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = appUrl + "/login";
                    }
                });
                return;
            }
            document.getElementById('negoModal').style.display = 'block';
        }

        function closeNegoModal() {
            document.getElementById('negoModal').style.display = 'none';
        }

        function submitNego() {
            var id_produk = $('#nego-price').data('id_produk');
            const quantity = $('#quantity-nego').val();
            const note = $('#note-nego').val();
            var price = unformatRupiah($('#nego-price').val());
            $.ajax({
                type: 'POST',
                url: '/api/calc_nego?token=' + token,
                data: {
                    id_produk: id_produk,
                    quantity: quantity,
                    nego_price: price,
                    note: note
                },
                success: function(response) {
                    if (response.error === 0) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Berhasil melakukan permintaan nego produk",
                            icon: "success",
                            confirmButtonText: "OK",
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: "Gagal melakukan permintaan nego. Silakan coba lagi.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    alert('melakukan permintaan nego. Silakan coba lagi.');
                }
            });
            closeNegoModal();
        }

        $('#nego-price').on('input', function() {
            var price = $(this).data('price')
            const quantity = $('#quantity-nego').val();
            const negoPrice = $(this).val().replace(/[^0-9]/g, '');
            if (negoPrice > price) {
                $(this).val(formatRupiah(price));
                update_total(quantity, price)
            } else {
                $(this).val(formatRupiah(negoPrice));
                update_total(quantity, negoPrice)
            }
        });

        function update_total(qty, price) {
            var total = (qty * price);
            $("#total-price-nego").val(formatRupiah(total));
        }

        function updateQuantityNego(amount) {
            var $quantityInput = $("#quantity-nego");
            var currentQuantity = parseInt($quantityInput.val());
            var maxStock = parseInt($quantityInput.attr("max"));
            var newQuantity = currentQuantity + amount;

            if (newQuantity >= 1 && newQuantity <= maxStock) {
                $quantityInput.val(newQuantity);
                updateButtonStates();
            }
        }

        function updateButtonStates() {
            var currentQuantity = parseInt($("#quantity-nego").val());
            var maxStock = parseInt($("#quantity-nego").attr("max"));
            var price = unformatRupiah($('#nego-price').val());
            update_total(currentQuantity, price)
            $(".quantity-btn-nego.minus").prop("disabled", currentQuantity <= 1);
            $(".quantity-btn-nego.plus").prop("disabled", currentQuantity >= maxStock);
        }

        updateButtonStates();
    </script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>