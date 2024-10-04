<!DOCTYPE html>
<html lang="id">
@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <section class="detaiProduct">
            <div class="gambarProduct">
                <!-- Gambar besar -->
                <div class="product-mobile">
                    @foreach ($gambarProduct as $gambar)
                    @php
                    $requiresBaseUrl = strpos($gambar->image300, 'http') === false;
                    $image300 = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $gambar->image300 : $gambar->image300;
                    @endphp
                    <div class="product-mobile-item">
                        <img src="{{ $image300 }}" alt="Gambar besar">
                    </div>
                    @endforeach
                </div>
                <div class="product-carousel">
                    @foreach ($gambarProduct as $gambar)
                    @php
                    $requiresBaseUrl = strpos($gambar->image300, 'http') === false;
                    $image300 = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $gambar->image300 : $gambar->image300;
                    @endphp
                    <div class="productdetail-item">
                        <img src="{{ $image300 }}" class="product-image-large">
                    </div>
                    @endforeach
                </div>

                <!-- Gambar kecil -->
                <div class="product-small">
                    @foreach ($gambarProduct as $gambar)
                    @php
                    $requiresBaseUrl = strpos($gambar->image50, 'http') === false;
                    $image50 = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $gambar->image50 : $gambar->image50;
                    @endphp
                    <img src="{{ $image50 }}" alt="Gambar kecil" class="product-image-small">
                    @endforeach
                </div>
            </div>
            <div class="product-detail">
                <div class="product-info-detail">
                    <h4><b>{{ $name }} </b></h4>
                    <div class="rate-product">
                        <ul>
                            <li>{{$count_rating}} Penilaian</li>
                            <li>{{$count_sold}} Terjual</li>
                        </ul>
                    </div>
                    <h3>Rp{{ number_format($hargaTayang, 0, ',', '.') }}</h3>
                    {{-- <p class="discount">
                        <span class="box">20%</span>
                        <span class="discounted-price">Rp100.000</span>
                    </p> --}}
                </div>

                <div class="quantity-selector">
                    <label for="quantity">Kuantitas :</label>
                    <div class="total-stock">

                        <div class="quantity-input">
                            <button type="button" class="quantity-btn minus">-</button>
                            <input type="number" id="quantity" name="quantity" min="1" max="{{$stock}}" value="1" step="1">
                            <button type="button" class="quantity-btn plus">+</button>
                        </div>
                        <p>Stok tersisa: <span id="stock-remaining">{{$stock}}</span></p>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="cart-btn" data-id_user="{{$id_user}}" data-tipe-user="{{ $member ? $member->id_member_type :'' }}" data-id="{{$id}}"><span class="material-icons">add_shopping_cart</span>
                        Keranjang</button>
                    <button class="buy-btn" data-id_user="{{$id_user}}" data-id="{{$id}}" data-tipe-user="{{ $member ? $member->id_member_type :'' }}">Beli Sekarang</button>
                    <button class="nego-btn" onclick="openNegoModal()" data-tipe-user="{{ $member ? $member->id_member_type :'' }}">Nego</button>
                </div>
                <div class="deskripsi-product">
                    <ul>
                        <li onclick="showSection('deskripsi')">Deskripsi</li>
                        <li onclick="showSection('speksifikasi')">Speksifikasi</li>
                        <li onclick="showSection('informasi-toko')">Informasi Toko</li>
                    </ul>
                </div>
                <div id="deskripsi" class="section-content">
                    <!-- Konten Deskripsi Produk -->
                    <p>
                        {{ $description !== null && $description !== '' ? $description : 'Toko tidak memasukkan deskripsi Barang' }}
                    </p>
                </div>
                <div id="speksifikasi" class="section-content" style="display: none;">
                    <p>
                        Merek : {{$merek}} <br>
                        {{ strip_tags($spesifikasi) }}
                    </p>
                </div>
                <div id="informasi-toko" class="section-content" style="display: none;">
                    <!-- Konten Informasi Toko -->
                    <p>Informasi toko di sini...</p>
                </div>
            </div>
            <div class="seller-detail">
                @php
                $requiresBaseUrl = strpos($avatar, 'http') === false;
                $icon_toko = $requiresBaseUrl ? "https://eliteproxy.co.id/seller_center/" .$avatar :$avatar;
                $requrl = strpos($image_banner, 'http') === false;
                $image_banner = $requrl ? "https://eliteproxy.co.id/" . $image_banner : $image_banner;
                @endphp
                <div class="toko_detail_produk">
                    <div class="toko-header" style="background-image: url('{{ $image_banner }}'); background-size: cover; background-position: center; width: 100%; height: 100px; position: relative;">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.3); border-radius: 10px;"></div>
                        <img src="{{ $icon_toko }}" alt="Avatar" class="toko-avatar" style="position: relative; z-index: 2; border-radius: 10px; margin-left: 20px; ">
                        <a href="{{ route('seller.detail', ['id' => $idToko]) }}" class="toko-nama" style="position: relative; z-index: 2; color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">
                            <b>{{ $namaToko }}</b>
                        </a>
                    </div>
                    <div class="toko-actions-detail-product">
                        <button class="btn-chat-detail-product">
                            <i class="material-icons">chat</i>
                            Chat
                        </button>
                        <a href="{{ route('seller.detail', ['id' => $idToko]) }}" class="btn-kunjungi">
                            <i class="material-icons">store</i>
                            Kunjungi Toko
                        </a>
                    </div>
                </div>
                <div class="produk-lainnya">
                    <p>Produk lainnya</p>
                    <a href="{{ route('seller.detail', ['id' => $idToko]) }}">
                        <p>lihat lainnya...</p>
                    </a>
                </div>
                <div class="product-seller">
                    @foreach ($produkToko as $product)
                    @php
                    $requiresBaseUrl = strpos($product->image300, 'http') === false;
                    $image300 = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$product->image300 : $product->image300;
                    @endphp

                    <a href="{{ route('product.show', ['id' => $product->id]) }}">
                        <img src="{{ $image300 }}" alt="Gambar produk">
                    </a>
                    @endforeach
                </div>
            </div>
        </section class="ulasan-product">
        <div class="Penlaian-product">
            <div class="action-cart-mobile">
                <button class="cart-btn" data-id="{{$id}}" data-tipe-user="{{ $member ? $member->id_member_type :'' }}"><span class="material-icons">add_shopping_cart</span>
                    Keranjang</button>
                <button class="buy-btn">Beli Sekarang</button>
                <button class="nego-btn" onclick="openNegoModal()">Nego</button>
            </div>

            <div class="deskripsi-product-mobile">
                <ul>
                    <li onclick="showSectionMobile('deskripsi-mobile')">Deskripsi</li>
                    <li onclick="showSectionMobile('speksifikasi-mobile')">Speksifikasi</li>
                    <li onclick="showSectionMobile('informasi-toko-mobile')">Informasi Toko</li>
                </ul>
            </div>
            <div id="deskripsi-mobile" class="section-content-mobile" style="display: none;">
                <!-- Konten Deskripsi Produk -->
                <p>Deskripsi produk di sini...</p>
            </div>
            <div id="speksifikasi-mobile" class="section-content-mobile" style="display: none;">
                <!-- Konten Speksifikasi Produk -->
                <p>Speksifikasi produk di sini...</p>
            </div>
            <div id="informasi-toko-mobile" class="section-content-mobile" style="display: none;">
                <!-- Konten Informasi Toko -->
                <p>Informasi toko di sini...</p>
            </div>

            <p><b>Penilaian Product</b></p>
            <div class="bintangproduct">
                <div class="penilaian-product-dekstop">
                    <ul class="review-options">
                        <li onclick="showReviewSection('all')">
                            Semua
                        </li>
                        <li onclick="showReviewSection('b1')">
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b2')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b3')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b4')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b5')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                    </ul>
                </div>

                <div class="penilaian-product-mobile">
                    <select id="review-options" onchange="showReviewSection(this.value)">
                        <option value="all">Semua</option>
                        <option value="b1">★ 1</option>
                        <option value="b2">★ 2</option>
                        <option value="b3">★ 3</option>
                        <option value="b4">★ 4</option>
                        <option value="b5">★ 5</option>
                    </select>
                </div>

                <div id="all" class="review-content">
                    <!-- Konten semua ulasan -->
                    <p>Semua ulasan di sini...</p>
                </div>
                <div id="b1" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 1 -->
                    <p>Ulasan bintang 1 di sini...</p>
                </div>
                <div id="b2" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 2 -->
                    <p>Ulasan bintang 2 di sini...</p>
                </div>
                <div id="b3" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 3 -->
                    <p>Ulasan bintang 3 di sini...</p>
                </div>
                <div id="b4" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 4 -->
                    <p>Ulasan bintang 4 di sini...</p>
                </div>
                <div id="b5" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 5 -->
                    <p>Ulasan bintang 5 di sini...</p>
                </div>
            </div>
        </div>
        <section>
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
                <input type="number" id="quantity-nego" name="quantity-nego" min="1" max="{{$stock}}" value="1" step="1">
                <button type="button" class="quantity-btn-nego plus" onclick="updateQuantityNego(1)">+</button>
            </div>
            <p>Stok tersisa: <span id="stock-remaining">{{$stock}}</span></p>

            <label for="initial-price">Harga Awal Satuan:</label>
            <input type="text" id="initial-price" value="Rp {{ number_format($hargaTayang, 0, ',', '.') }}" readonly>

            <label for="nego-price">Harga Nego Satuan:</label>
            <input type="text" data-id_produk="{{ $id }}" data-price="{{ $hargaTayang }}" id="nego-price">

            <label for="total-price">Harga Nego Total:</label>
            <input type="text" id="total-price-nego" readonly>

            <label for="note-nego">Catatan:</label>
            <textarea id="note-nego"></textarea>

            <button style="margin-top: 40px;" onclick="submitNego()" class="nego-btn">Kirim Nego</button>
        </div>
    </div>

    @include('member.asset.footer')
    <script>
        function openNegoModal() {
            var id_user = $(".cart-btn").data("id_user");
            var tipe_user = $(".buy-btn").data("tipe-user");
            if (tipe_user != '') {
                if (tipe_user !== 3) {
                    Swal.fire({
                        title: "Perhatian",
                        text: "Anda tidak di Perbolehkan Nego Barang.",
                        icon: "warning",
                        confirmButtonText: "Tutup",
                        cancelButtonText: "Batal",
                    });
                    return;
                }
            }

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
                url: '/api/calc_nego',
                data: {
                    id_produk: id_produk,
                    quantity: quantity,
                    nego_price: price,
                    note: note
                },
                success: function(response) {
                    if (response.error === 0) {
                        alert('Berhasil menghitung nego harga. Silakan lanjutkan dengan proses berikutnya.');
                    } else {
                        alert('Gagal menghitung nego harga. Silakan coba lagi.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menghitung nego harga. Silakan coba lagi.');
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
    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>