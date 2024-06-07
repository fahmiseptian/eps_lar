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
                    <div class="detail-transaksi">
                        <div id="nomor-inv">
                            <p>INV-2232323232-222</p>
                        </div>
                        <div id="status-payment">
                            <p>Belum Bayar</p>
                        </div>
                    </div>
                    {{-- alamat --}}
                    <div class="alamat-penerima">
                        <div id="header-alamat-penerima">
                            <p><span class="material-icons">location_on</span> Alamat Pengiriman</p>
                        </div>
                        <div class="detail-alamat-penerima">
                            <div class="item-alamat-penerima">
                                <p>Instansi</p>
                                <p>Test &nbsp;| 0927218282</p>
                            </div>
                            <div class="item-alamat-penerima">
                                <p>Nama</p>
                                <p>alamat</p>
                            </div>
                        </div>
                    </div>
                    <div class="data-transaksi-penjual">
                        <div class="item-transaksi-penjual">
                            <div class="row">
                                <div class="label"><b>Penjual</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>NPWP</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Tanggal Dibuat</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Pemohon</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Departemen</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Tipe Pembayaran</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>TOP</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                        </div>
                        <div class="item-transaksi-penjual">
                            <div class="row">
                                <div class="label"><b>Untuk Keperluan</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Alamat Pengiriman</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Alamat Penagihan</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Penerima</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>No Telpon Penerima</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                            <div class="row">
                                <div class="label"><b>Pesan ke Penjual</b></div>
                                <div class="pemisah">:</div>
                                <div class="value">Toko</div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="row">
                            <div class="col-md-5"></div>
                            <div class="col-md-7">
                                <div class="detail-pembayaran">
                                    <p>Subtotal Product tanpa PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Subtotal produk sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Subtotal Ongkos Kirim sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Subtotal Asuransi Pengiriman sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Biaya Penanganan sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="total-pembayaran">
                                    <p>Grand Total</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="detail-product-transaksi">
                        <div class="toko-info">
                            <p>Nama Toko</p>
                        </div>
                        <div class="btn-group">
                            <p class="btn"><span class="material-icons">content_copy</span>Lacak Pesanan</p>
                            <p class="btn"><span class="material-icons">content_copy</span>Kwitansi</p>
                            <p class="btn"><span class="material-icons">content_copy</span>Invoice</p>
                            <p class="btn"><span class="material-icons">content_copy</span>Kontrak</p>
                        </div>
                    </div>
                    <div class="product-transaksi">
                        <div class="product-list">
                            <div class="product-item">
                                <div class="product-number">1.</div>
                                <div class="product-image">
                                    <img src="#gambar" alt="product" width="50" height="50">
                                </div>
                                <div class="product-name">Nama Barang</div>
                                <div class="product-price">Rp 80.000</div>
                                <div class="product-quantity">1</div>
                                <div class="product-total">Rp 80.000</div>
                            </div>
                            <!-- Tambahkan item produk lainnya dengan pola yang sama -->
                        </div>
                    </div>

                </div>





                <div class="main-pengaturan" id="main-pengaturan">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-5">
                                <p>Metode Pembayaran : <b>Transfer Bank</b> <br> <b> Bank BNI </b> <br> <br> No Rekening <b>03975-60583</b> a/n <br> <b>PT. Elite Proxy Sistem</b> </p>
                            </div>
                            <div class="col-md-7">
                                <div class="detail-pembayaran">
                                    <p>Subtotal Product tanpa PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Subtotal produk sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Subtotal Ongkos Kirim sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Subtotal Asuransi Pengiriman sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>Biaya Penanganan sebelum PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="detail-pembayaran">
                                    <p>PPN</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                <div class="total-pembayaran">
                                    <p>Total</p>
                                    <p>Rp. {{ number_format(10000, 0, ',', '.') }}</p>
                                </div>
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-5">
                                </div>
                                <div class="col-md-7">
                                    <p class="btn btn-primary">Kembali</p>
                                    <p class="btn btn-success">Upload Pembayaran</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
</body>

</html>
