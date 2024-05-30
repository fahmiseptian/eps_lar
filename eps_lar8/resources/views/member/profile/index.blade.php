<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <!-- Data Section -->
        <section class="dahsboard-awal">
            <div class="detail-profile">
                <div class="profile-avatar">
                    <img src="http://127.0.0.1:8001/assets/images/avatar-m.jpg" alt="Profile Avatar">
                    <div class="profile-username">
                        <p>PP 10</p>
                        <small>ubah profile</small>
                    </div>
                </div>
                <div class="separator"></div> <!-- Garis pemisah -->
                <div class="menu-profile" >
                    <ul>
                        <li id="menu-dashboard" data-id=""><i class="material-icons">dashboard</i> Dashboard</li>
                        <li id="menu-transaksi"><i class="material-icons">receipt</i> Transaksi</li>
                        <li id="menu-profile"><i class="material-icons">chat</i> Negosiasi</li>
                        <li id="menu-profile"><i class="material-icons">favorite</i> Favorit</li>
                        <li id="menu-profile"><i class="material-icons">visibility</i> Terakhir Dilihat</li>
                        <li id="menu-profile"><i class="material-icons">manage_accounts</i> Manajemen</li>
                        <li id="menu-profile"><i class="material-icons">settings</i> Pengaturan</li>
                        <li id="menu-profile"><i class="material-icons">exit_to_app</i> Keluar</li>
                    </ul>
                </div>
            </div>
            <div class="pengaturan-profile">
                <div class="judul-pengaturan" id="judul-pengaturan">
                    <div class="list-menu-transaksi">
                        <ul>
                            <li class="active">Semua</li>
                            <li>Butuh Persetujuan</li>
                            <li>Disetujui</li>
                            <li>Ditolak</li>
                            <li>Dikirim</li>
                        </ul>
                    </div>
                </div>
                <div class="main-pengaturan" id="main-pengaturan">
                    <div class="list-transaksi">
                        {{-- Transaksi --}}
                        <div class="item-transaksi">
                            <p style="text-align: right; color:red">Pembayaran</p>
                            {{-- Seller --}}
                            <p style="font-size: 18px"><b>INV 2310011232</b></p>
                            <div class="item-product-transaksi">
                                <div style="display: flex; justify-content: space-between;">
                                    <p>PT. Elite Proxy Sistem</p>
                                    <p>Menunggu konfirmasi penjual</p>
                                </div>
                                {{-- Product --}}
                                <div style="display: flex; align-items: center;">
                                    <p style="margin-right: 10px;">1.</p>
                                    <img src="http://127.0.0.1:8001/seller_center/upload/product/300-product_0_800-product0WhatsAppImage2024-02-29at154128_.jpeg" style="width:50px; height:50px; margin-right: 10px;" alt="product">
                                        <p>nama Barang <br> 2 x Rp. 129.870</p>
                                    <p style="margin-left: auto; text-align: right;"><b>Total </b> <br> Rp254.000</p>
                                </div>
                                {{-- end Product --}}
                            </div>
                            {{-- end Seller --}}
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                    <p>Total Pesanan</p>
                                    <p>Pesanan dibuat <br> 2024-05-15</p>
                                </div>
                                <div>
                                    <p style="text-align: right">Total Harga <b>Rp.300.000</b></p>
                                    <p><button class="btn btn-primary" style="width:100%">Detail</button></p>
                                </div>
                            </div>
                        </div>
                        {{-- end Transaksi --}}
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
</body>

</html>
