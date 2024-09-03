<div style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
    <div style="padding: 20px;">
        <div style="position: relative; height: 4px; margin-top: 100px; margin-bottom: 20px; background-color: black;">
            <div
                style="position: absolute; top: 50%; left: -8px; transform: translateY(-50%); width: 8px; height: 8px; background-color: black; border-radius: 50%;">
            </div>
            <div
                style="position: absolute; top: 50%; right: -8px; transform: translateY(-50%); width: 8px; height: 8px; background-color: black; border-radius: 50%;">
            </div>
        </div>
        <table style="width: 100%; border: none; margin-top:0">
            <tr>
                <td style="border: none; vertical-align:top">
                    <table style="width: 100%; font-size: 12px; border: none;">
                        <!-- <tr>
                            <td style="width: 140px; border: none;">Kategori</td>
                            <td style="width: 14px; border: none;">:</td>
                            <td style="border: none;">Elektronik</td>
                        </tr> -->
                        <tr>
                            <td style="width: 100px; border: none;">Nomor Pembelian</td>
                            <td style="width: 10px; border: none;">:</td>
                            <td style="border: none;">{{ $order->invoice . '-' . $order->id_cart_shop }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Penjual</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:blue">(Nama PT/seller) </td>
                        </tr>
                        <tr>
                            <td style="border: none;">NPWP Penjual</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:blue">(NPWP PT/seller) </td>
                        </tr>
                        <tr>
                            <td style="border: none;">Tanggal Dibuat</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">.../.../2024</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Pemohon</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Instansi</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Instansi)</td>
                        </tr>
                        <tr>
                            <td style="border: none;">NPWP</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                    </table>
                </td>
                <td style="border: none;">
                    <table style="width: 100%; font-size: 12px; border: none;">
                        <tr>
                            <td style="width: 100px; border: none;">Alamat Pengiriman</td>
                            <td style="width: 10px; border: none;">:</td>
                            <td style="border: none; vertical-align:top;">
                                {{ $order->address }}, {{ $order->subdistrict_name }} <br>
                                {{ $order->city }}, {{ $order->province_name }} , ID {{ $order->postal_code }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none;">Alamat Penagihan</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; vertical-align:top;">
                                Testing area, Pamulang <br>
                                Tangerang Selatan, Banten, ID 15417
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none;">Penerima</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                        <tr>
                            <td style="border: none;">No Telpon Penerima</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Tipe Pembayaran</td>
                            <td style="border: none;">:</td>
                            <td style="border: none;"> 30 TOP
                            </td>
                        </tr>
                        <tr>
                            <td style="border: none;">Tahun Anggaran</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Harga Total</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; vertical-align:top;">
                                Rp <?= number_format($order->total, 0, ',', '.') ?>
                                <br>
                                {{ $order->terbilang }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: none;">
                    <table style="width: 100%; font-size: 12px; border: none;">
                        <tr>
                            <td style="width: 100px; border: none;">Untuk Keperluan</td>
                            <td style="width: 10px; border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Sumber Dana</td>
                            <td style="border: none;">:</td>
                            <td style="border: none; color:red">(Diisi oleh pihak dinas)</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width: 100%; font-size: 12px; border: none; margin-top:40px; text-align:left">
            <tr>
                <th style="border: none; width:65%; text-align:left">Pemesan</th>
                <th style="border: none;width:10%"></th>
                <th style="border: none;text-align:left">Seller</th>
            </tr>
            <tr>
                <td style="border: none; vertical-align:top; color:blue">
                    (Diisi oleh pihak dinas)
                </td>
                <td style="border: none; vertical-align:top; color:blue">
                </td>
                <td style="border: none;  vertical-align:top; color: red;">
                    (Nama PT/seller) <br>
                    (Alamat PT/seller) <br>
                    (Email PT/seller) <br>
                    (No Telp PT/seller)
                </td>
            </tr>
        </table>
        <div style="position: relative; height: 4px; margin-top: 40px; margin-bottom: 20px; background-color: black;">
            <div
                style="position: absolute; top: 50%; left: -8px; transform: translateY(-50%); width: 8px; height: 8px; background-color: black; border-radius: 50%;">
            </div>
            <div
                style="position: absolute; top: 50%; right: -8px; transform: translateY(-50%); width: 8px; height: 8px; background-color: black; border-radius: 50%;">
            </div>
        </div>
        <div>
            <h2>Daftar Pesanan</h2>
            @foreach ($order->detail as $item)
                <table style="width: 100%; font-size: 14px; border-collapse: collapse; margin-bottom:10px">

                    <tr>
                        <td
                            style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold; width: 200px;">
                            Nama Produk</td>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">
                            {{ $item->nama_produk }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;">
                            Brand</td>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">
                            {{ $item->nama_brand }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;">
                            Kuantitas</td>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">
                            {{ $item->qty_produk }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;">
                            Harga Satuan</td>
                        <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">Rp.
                            <?= number_format($item->harga_satuan_produk, 0, ',', '.') ?> </td>
                    </tr>
                </table>
            @endforeach
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td
                        style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;width: 200px;">
                        Ongkos Kirim</td>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">Rp.
                        <?= number_format($order->sum_shipping + $order->ppn_shipping, 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;">
                        Tanggal Pengiriman</td>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">
                        {{ $order->pengiriman }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;">
                        Total Harga</td>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">Rp.
                        <?= number_format($order->total, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9; font-weight: bold;">
                        Catatan</td>
                    <td style="border: 1px solid #ccc; padding: 8px; background-color: #D9D9D9;">{{ $order->status }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
