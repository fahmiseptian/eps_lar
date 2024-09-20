<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    // Menghitung biaya penanganan dan PPN
    $handling_cost = $data->handling_cost / (1 + $data->val_ppn / 100);
    $ppn_handling_cost = $data->handling_cost - $handling_cost;

    // Menginisialisasi variabel proforma
    $proforma = false;

    // Cek kondisi untuk menentukan nilai proforma
    if (in_array($data->id_payment, [23, 30, 31])) {
        $proforma = ($data->status_pembayaran_top == '0');
    } elseif ($data->id_payment == 22) {
        $proforma = !in_array($data->invoice_status, ['complete_payment', 'completed']);
    }
    ?>

    <title><?= $proforma ? 'Proforma Invoice' : 'Invoice' ?></title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', 'sans-serif';
            font-size: 14px;
            margin: 0;
            padding: 0;
            background: url('{{ public_path('img/app/inv-logo-eps.png') }}') no-repeat center;
            background-size: 80%;
        }


        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th, .invoice-table td {
            border: 2px solid orange;
            padding: 8px;
            text-align: left;
        }

        .subtotal-row td {
            border-top: 2px solid #FC6703;
            text-align: right;
        }

        .left {
            width: 50%;
            float: left;
            text-align: left;
            margin-right: 5px;
        }

        .right {
            width: 50%; /* Lebar elemen */
            float: right; /* Mengatur agar elemen berada di sebelah kanan */
            text-align: right; /* Mengatur teks ke kanan di dalam elemen */
            margin-left: 5px; /* Margin kiri untuk memberi jarak dari elemen lain */
            box-sizing: border-box; /* Menyertakan padding dalam perhitungan lebar elemen */
            font-size: 12px
        }

    </style>
</head>
<body>
    <div style="text-align: center">
        <h2 style="margin-bottom: 0;">{{ $proforma ? 'PROFORMA INVOICE' : 'INVOICE' }}</h2>
        <small style="margin-top: 0;">
            No. {{ $proforma ? 'Proforma' : 'Invoice' }} : {{ $proforma ? 'PRF' . $data->invoice : $data->invoice }} - {{ $data->id_cart_shop }}
            <br>
            Tanggal {{$proforma ? '' : ''}} : {{$proforma ? date('d-m-Y', strtotime($data->created_date)) : date('d-m-Y', strtotime($data->tanggal_bayar))}}
        </small>
    </div>
    <div class="detail_order">
        <table style="width:100%; margin-top:20px">
            <tr>
                <th> Pembeli </th>
                <th style="width: 35%;"> Rekanan </th>
                <th> Pihak Lain </th>
            </tr>
            <tr>
                <td style="vertical-align:top">
                    Instansi : {{$dataPembeli->instansi}} <br>
                    Satker : {{$dataPembeli->satker}} <br>
                    NPWP : {{$dataPembeli->npwp}} <br>
                    Alamat : {{$dataPembeli->address}}, {{$dataPembeli->subdistrict_name}},
                    {{$dataPembeli->city}}, {{$dataPembeli->province_name}}, ID {{$dataPembeli->postal_code}}
                </td>
                <td style="vertical-align:top">
                    Nama : {{$dataSeller->nama_pt}} <br>
                    NPWP : {{$dataSeller->npwp}} <br>
                    Alamat : {{$dataSeller->address}}, {{$dataSeller->subdistrict_name}},
                    {{$dataSeller->city_name}}, {{$dataSeller->province_name}}, ID {{$dataSeller->postal_code}}
                </td>
                <td style="vertical-align:top">
                    Nama : PT.Elite Proxy Sistem <br>
                    NPWP : 73.035.456.0-022.000 <br>
                    Alamat : Rukan Sudirman Park
                    Apartement Jl Kh. Mas Mansyur KAV 35
                    A/15 Kelurahan Karet Tengsin Kec. Tanah
                    Abang Jakarta Pusat DKI Jakarta
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table class="invoice-table">
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Jenis Barang / Jasa</th>
                <th style="width: 5%;">QTY</th>
                <th style="width: 25%;">Harga Sebelum Pajak</th>
                <th style="width: 25%;">Jumlah</th>
            </tr>
            {{-- PHP --}}
            <?php
            $i = 1;
                $total_product_before_ppn = 0;
                $total_product_non_ppn = 0;
                foreach ($data->detail as $p) {
                    if ($p->val_ppn != 0) {
                        $total_product_before_ppn += $p->total_non_ppn;
                    }
                    else {
                        $total_product_non_ppn += $p->total_non_ppn;
                    }
            ?>
            <tr>
                <td scope="row"><?= $i ?></td>
                <td><?= $p->nama_produk ?> <?= $data->val_ppn == '0' ? '<b>(Barang Tidak Kena PPN)</b>' : '' ?>
                    <?= $p->id_nego != null ? '' : '' ?></td>
                <td><?= $p->qty_produk ?></td>
                <td>Rp
                    <?= $data->val_ppn == '0' ? number_format((float) $p->base_price, 2, ',', '.') : number_format((float) $p->harga_dasar_lpse, 2, ',', '.') ?>
                </td>
                <td>Rp <?= number_format((float) $p->total_non_ppn, 2, ',', '.') ?></td>
            </tr>
            <?php
                $i++;
                }
            ?>
            <tr>
                <td scope="row"><?= $i ?></td>
                <td>Ongkos Kirim <?= $data->deskripsi ?></td>
                <td>1</td>
                <td>Rp <?= number_format((float) $data->sum_shipping + $data->insurance_nominal, 0, '.', '.') ?>
                </td>
                <td>Rp <?= number_format((float) $data->sum_shipping + $data->insurance_nominal, 0, '.', '.') ?>
                </td>
            </tr>
            <?php if ($data->handling_cost_non_ppn > 0) { ?>
            <tr>
                <td scope="row"><?= $i ?></td>
                <td>Biaya Penanganan</td>
                <td>1</td>
                <td>Rp {{number_format((float) $data->handling_cost_non_ppn, 0, '.', '.')}} </td>
                <td>Rp {{ number_format((float) $data->handling_cost_non_ppn, 0, '.', '.')}}</td>
            </tr>
            <?php } ?>

            <tr class="subtotal-row">
                <td colspan="3" style="border: transparent;"></td>
                <td style="text-align:center">Subtotal</td>
                <td style="text-align:left">Rp {{number_format((float) $data->subtotal, 0, '.', '.')}}</td>
            </tr>
        </table>
    </div>
    <br>
    <div class="left">
        <table class="invoice-table">
            <tr>
                <th> Tarif </th>
                <th> DPP Barang / Jasa</th>
                <th> PPh 22 Barang / Jasa</th>
            </tr>
            <tr>
                <td>  {{$data->val_pph}} </td>
                <td> Rp {{number_format((float) $total_product_non_ppn + $total_product_before_ppn + $data->handling_cost_non_ppn, 0, '.', '.')}} </td>
                <td> Rp {{number_format((float) $data->pph_price, 0, '.', '.')}} </td>
            </tr>
        </table>
        <br>
        <table class="invoice-table">
            <tr>
                <th> Tarif </th>
                <th> DPP Ongkos Kirim </th>
                <th> PPh 22 Barang / Jasa</th>
            </tr>
            <tr>
                <td> {{$data->val_pph}} </td>
                <td> Rp {{number_format((float) $data->sum_shipping + $data->insurance_nominal, 0, '.', '.')}} </td>
                <td> Rp {{number_format((float) $data->pph_shipping, 0, '.', '.')}} </td>
            </tr>
        </table>
    </div>
    <div class="right">
        <table class="invoice-table" style="width:70%; margin-left:100px">
            <tr>
                <td style="width: 50%; border: transparent; text-align:right;"> <b> DPP Barang / Jasa </b> </td>
                <td> Rp {{number_format((float) ($total_product_before_ppn + $data->handling_cost_non_ppn), 0, '.', '.')}} </td>
            </tr>
            <tr>
                <td style="width: 50%; border: transparent; text-align:right;"> <b> PPN Barang / Jasa </b> </td>
                <td> Rp {{ number_format((float) $data->ppn_price, 0, '.', '.')}} </td>
            </tr>
        </table>
        <br>
        <table class="invoice-table" style="width:70%; margin-left:100px;">
            <tr>
                <td style="width: 50%; border: transparent; text-align:right;"> <b> DPP Ongkos Kirim </b> </td>
                <td> Rp {{number_format((float) ($data->sum_shipping + $data->insurance_nominal), 0, '.', '.')}} </td>
            </tr>
            <tr>
                <td style="width: 50%; border: transparent; text-align:right;"> <b> PPN Ongkos Kirim </b> </td>
                <td> Rp {{number_format((float) $data->ppn_shipping, 0, '.', '.')}} </td>
            </tr>
        </table>
        <br>
        <table class="invoice-table" style="width:70%; margin-left:100px;">
            <tr>
                <td style="width: 50%; border: transparent; text-align:right;"> <b> Grand Total </b> </td>
                <td> Rp {{number_format((float) $data->total, 0, '.', '.')}} </td>
            </tr>
        </table>
    </div>
    <br>
    @if ($proforma)
        <div style="clear: both; margin-top: 60px">
            <div class="left">
                @if ($data->id_payment == 23)
                    <p>
                        <b>Catatan</b> <br>
                        Pembayaran :
                        <b> BNI Cabang Sudirman Park </b>
                        A/N : <b> PT. Elite Proxy Sistem </b>
                        No : <b> 03975-60583 </b>
                    </p>
                    @if ($data->province_name == 'Banten')
                        <p>
                            <b>Catatan</b> <br>
                            Pembayaran :
                            <b> Bank Banten KC Kelapa Gading </b>
                            A/N : <b> PT. Elite Proxy Sistem </b>
                            No : <b> 0302010447 </b>
                        </p>
                    @elseif ($data->province_name == 'DI Yogyakarta')
                        <p>
                            <b>Catatan</b> <br>
                            Pembayaran :
                            <b> BPD DIY Cabang Yogyakarta </b>
                            A/N : <b> PT. Elite Proxy Sistem </b>
                            No : <b> 001111002037 </b>
                        </p>
                    @endif
                @elseif ($data->id_payment == 30 || 31)
                    <p>
                        <b>Catatan</b> <br>
                        Pembayaran :
                        <b> Virtual Account </b> <br>
                        No Virtual Account : <b> {{$data->va_number}}</b>
                    </p>
                @endif
            </div>
            <div class="right" style="text-align: right">
                <p>
                    Sesuai PMK Nomor 58/PMK.03/2022 <br>
                    Nilai yang harus dibayarkan Rp {{number_format((float) $data->total, 0, '.', '.')}} <br>
                    PPN dan PPh akan disetorkan oleh <br>
                    PT. Elite Proxy Sistem <br>
                    (Pihak Lain yang ditunjuk sebagai Pemungut Pajak)
                </p>
            </div>
        </div>
    @else
        <div style="clear: both; margin-top: 60px">
            <div class="left">
                <p>
                    *Invoice ini berlaku sebagai Bukti Pungut PPh Pasal 22 dan dokumen tertentu
                    yang kedudukannya dipersamakan dengan Faktur Pajak
                </p>
            </div>
            <div class="right" style="text-align: right">
                <p>
                    **Transaksi ini telah dikenakan pph pasal 22 sebesar {{$data->val_pph}} % ke penyedia dan
                    mitra pengiriman dari nilai invoice diluar PPN
                </p>
            </div>
        </div>
    @endif
</body>
</html>
