<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $handling_cost = $data->handling_cost / (1 + $data->val_ppn / 100);
    $ppn_handling_cost = $data->handling_cost - $handling_cost;
    $proforma = false;
    
    if (in_array($data->id_payment, [23, 30])) {
        if ($data->status_pembayaran_top == '0') {
            $proforma = true;
        }
    } elseif ($data->id_payment == 22) {
        // NOTE Kartu Kredit
        if ($data->invoice_status != 'complete_payment' && $data->invoice_status != 'completed') {
            $proforma = true;
        }
    }
    
    ?>


    <meta charset="utf-8" />
    <title><?= $proforma ? 'Proforma Invoice' : 'Invoice' ?></title>

    <!-- JavaScript Bundle with Popper -->
    <style>

        @font-face {
            font-family: corbel;
            src: url(<?= 'assets/font/seller/CORBEL.TTF' ?>);
        }

        @font-face {
            font-family: corbelb;
            src: url(<?= 'assets/font/seller/CORBELB.TTF' ?>);
        }

        @font-face {
            font-family: Helvetica;
            src: url(<?= 'assets/font/helvetica/Helvetica.ttf' ?>);
        }

        @font-face {
            font-family: HelveticaB;
            src: url(<?= 'assets/font/helvetica/Helvetica-Bold.ttf' ?>);
        }

        table.table-bordered {
            border-bottom: none !important;
            border-left: none !important;
            border-right: 2px solid #fc6703 !important;
            border-top: 2px solid #fc6703 !important;
        }

        table.table-bordered thead th {
            border-top: none;
            border-bottom: 2px solid #fc6703;
            border-left: 2px solid #fc6703 !important;
            border-right: 2px solid #fc6703 !important;
        }

        table.table-bordered td {
            border-left: 2px solid #fc6703 !important;
            border-right: 2px solid #fc6703 !important;
            border-top: none !important;
            border-bottom: none !important;
        }

        

        /* Styling */
        body {
            font-family: 'Helvetica', sans-serif;
            background: url('{{ public_path('img/app/inv-logo-eps.png') }}') no-repeat center;
            background-size: 65%;
        }

        h1 {
            font-size: 28px;
        }

        p {
            font-size: 12px;
        }

        .equal {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="text-center" style="font-family: corbelb;">
            <h1 style="font-size: 28px;">{{ $proforma ? 'PROFORMA INVOICE' : 'INVOICE' }}
                <div style="font-family: helvetica; font-size:12px; font-weight:normal;">
                    <p>
                        No. {{ $proforma ? 'Proforma' : 'Invoice' }} :
                        {{ $proforma ? 'PRF' . $data->invoice : $data->invoice }} - {{ $data->id_cart_shop }} <br />

                        Tanggal <?= $proforma ? '' : '' ?> :
                        <?= $proforma ? date('d-m-Y', strtotime($data->created_date)) : date('d-m-Y', strtotime($data->tanggal_bayar)) ?><br />
                    </p>
                </div>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <td width="25%">
                            <p style="font-family: helveticab; font-size: 14px;">Pembeli</p>
                            <p style="font-size: 12px;">
                                Instansi : <?= $data->instansi . '<br />' ?>
                                Satker : <?= $data->satker ?> <br />
                                NPWP : <?= $data->npwp ?: '-' ?><br />
                                Alamat :
                                <?= $data->npwp_address ?: $data->address . ', ' . $data->subdistrict_name . ', ' . $data->city_name . ', ' . $data->province_name . ', ID ' . $data->postal_code ?>
                                <br />
                            </p>
                        </td>
                        <td scope="col" width="25%">
                            <p style="font-family: helveticab; font-size: 14px;">Rekanan</p>
                            <p style="font-size: 12px;">
                                @foreach ($data->seller_address as $sla)
                                    Nama : <?= $sla->name ?> <br />
                                    NPWP : <?= $sla->npwp ?> <br />
                                    Alamat : <?= $sla->npwp_address ?><br />
                                @endforeach

                            </p>
                        </td>
                        <?php
                            if ($data->sum_shipping > 0) {
                            ?>
                        <td scope="col" width="25%">
                            <p style="font-family: helveticab; font-size: 14px;">Rekan Ekspedisi</p>
                            <p style="font-size: 12px;">
                                Nama : <?= $data->nama_pt_courier ?> <br />
                                NPWP : <?= $data->npwp_courier ?> <br />
                                Alamat : <?= $data->alamat_npwp_courier ?><br />
                            </p>
                        </td>
                        <?php } ?>
                        <td width="25%">
                            <p style="font-family: helveticab; font-size: 14px;">Pihak lain</p>
                            <p style="font-size: 12px;">
                                Nama : <?= $eps['nama'] ?><br />
                                NPWP : <?= $eps['npwp'] ?><br />
                                Alamat : <?= $eps['alamat'] ?><br />
                            </p>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-bordered" style="font-size: 12px; width:100%" >
                <thead>
                    <tr>
                        <th scope="col" width="5%">No.</th>
                        <th scope="col" width="40%">Jenis Barang / Jasa</th>
                        <th scope="col" width="5%">Qty</th>
                        <th scope="col" width="20%">Harga Sebelum Pajak</th>
                        <th scope="col" width="20%">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
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
                        <td>Rp <?= number_format((float) $data->handling_cost_non_ppn, 0, '.', '.') ?></td>
                        <td>Rp <?= number_format((float) $data->handling_cost_non_ppn, 0, '.', '.') ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td scope="row" colspan="3" class="text-center"
                            style="border-left: none !important ;border-top: 2px solid #fc6703 !important;"></td>
                        <td scope="row" class="text-center" style="border: 2px solid #fc6703 !important;">Sub Total
                        </td>
                        <td style="border: 2px solid #fc6703 !important;">Rp
                            <?= number_format((float) $data->subtotal, 0, '.', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-xs-5">
            <table class="table table-bordered" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th scope="col" width="20%">Tarif</th>
                        <th scope="col" width="40%">DPP Barang / Jasa</th>
                        <th scope="col" width="40%"><?php if (in_array($data->invoice, array('INV-20221027462', 'INV-20221027461', 'INV-20221027460', 'INV-20221027458', 'INV-20221027456', 'INV-20221027454', 'INV-20221027452'))) { ?>PPh 23<?php } else { ?>PPh
                            22<?php } ?> Barang / Jasa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row" style="border: 2px solid #fc6703 !important;"><?= $data->val_pph ?></td>
                        <td style="border: 2px solid #fc6703 !important;">Rp
                            <?= number_format((float) $total_product_non_ppn + $total_product_before_ppn + $data->handling_cost_non_ppn, 0, '.', '.') ?>
                        </td>
                        <td style="border: 2px solid #fc6703 !important;">Rp
                            <?= number_format((float) $data->pph_price, 0, '.', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-xs-2">
        </div>
        <div class="col-xs-4" style="float:right; margin-right: -4%;">
            <table class="table" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th scope="col" class="text-right" style="border: none !important;">DPP Barang / Jasa</th>
                        <td scope="col" width="50%" style="border: 1px solid #fc6703;">Rp
                            <?= number_format((float) ($total_product_before_ppn + $data->handling_cost_non_ppn), 0, '.', '.') ?>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row" class="text-right" style="border: none !important;">PPN Barang / Jasa</th>
                        <td style="border: 1px solid #fc6703;">Rp
                            <?= number_format((float) $data->ppn_price, 0, '.', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-5">
            <table class="table table-bordered" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th scope="col" width="20%">Tarif</th>
                        <th scope="col" width="40%">DPP Ongkos Kirim</th>
                        <th scope="col" width="40%"><?php if (in_array($data->invoice, array('INV-20221027462', 'INV-20221027461', 'INV-20221027460', 'INV-20221027458', 'INV-20221027456', 'INV-20221027454', 'INV-20221027452'))) { ?>PPh 23<?php } else { ?>PPh
                            22<?php } ?> atas Ongkos Kirim</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row" style="border: 2px solid #fc6703 !important;"><?= $data->val_pph ?></td>
                        <td style="border: 2px solid #fc6703 !important;">Rp
                            <?= number_format((float) $data->sum_shipping + $data->insurance_nominal, 0, '.', '.') ?>
                        </td>
                        <td style="border: 2px solid #fc6703 !important;">Rp
                            <?= number_format((float) $data->pph_shipping, 0, '.', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-xs-2">
        </div>
        <div class="col-xs-4" style="float:right; margin-right: -4%;">
            <table class="table" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <th scope="col" class="text-right" style="border: none !important;">DPP Ongkos Kirim</th>
                        <td scope="col" width="50%" style="border: 1px solid rgb(252, 103, 3);">Rp
                            <?= number_format((float) ($data->sum_shipping + $data->insurance_nominal), 0, '.', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" class="text-right" style="border: none !important;">PPN Ongkos Kirim</th>
                        <td style="border: 1px solid #fc6703;">Rp
                            <?= number_format((float) $data->ppn_shipping, 0, '.', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-5">
            <p>&nbsp;</p>
        </div>
        <div class="col-xs-3">
            <p>&nbsp;</p>
        </div>
        <div class="col-xs-4" style="float:right; margin-right: -4%;">
            <table class="table" style="font-size: 12px;">
                <tbody>
                    <tr>
                        <th scope="row" class="text-right" style="border: none !important;">Grand Total</th>
                        <td style="border: 1px solid #fc6703;">Rp
                            <?= number_format((float) $data->total, 0, '.', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    if ($proforma) {
    ?>
    <div class="container">
        <div class="row">
            <table width="100%">
                <tr>
                    <td width="55%" class="px-0 mx-0">
                        <p style="font-family: helveticab; margin-bottom:5px;">Catatan</p>
                        <div style="font-family: helvetica; font-size: 12px;">
                            <p>
                                Pembayaran : <br />
                                <?php if($data->id_payment == 23) { ?>
                            <table width="100%">
                                <td width="50%" class="px-0 mx-0">
                                    <b>BNI Cabang Sudirman Park</b><br />
                                    A/N : <b>PT. Elite Proxy Sistem</b><br />
                                    No : <b>03975-60583</b><br />
                                </td>
                                <?php if($data->province_name == 'Banten') { ?>
                                <td width="50%" class="px-0 mx-0">
                                    <b>Bank Banten KC Kelapa Gading</b><br />
                                    A/N : <b>PT. Elite Proxy Sistem</b><br />
                                    No : <b>0302010447</b><br />
                                </td>
                                <?php } ?>
                                <?php if($data->province_name == 'DI Yogyakarta') { ?>
                                <td width="50%" class="px-0 mx-0">
                                    <b>BPD DIY Cabang Yogyakarta</b><br />
                                    A/N : <b>PT. ELITE PROXY SISTEM</b><br />
                                    No : <b>001111002037</b><br />
                                </td>
                                <?php } ?>
                            </table>
                            <?php } ?>

                            <?php if($data->id_payment == 30) { ?>
                            Virtual Account BCA<br />
                            <b><?= $data->va_number ?></b>
                            <?php } ?>
                            </p>
                        </div>
                    </td>
                    <td width="45%" class="px-0 mx-0">

                        <?php if (in_array($data->invoice, array('INV-20221027462', 'INV-20221027461', 'INV-20221027460', 'INV-20221027458', 'INV-20221027456', 'INV-20221027454', 'INV-20221027452'))) { ?>
                        <p class="text-right" style="font-family: helvetica; font-size: 12px;">
                            Sesuai PMK Nomor <b>59/PMK.03/2022</b><br>
                            Nilai yang harus dibayarkan <b>Rp
                                <?= number_format((float) ($data->sum_price_non_ppn - $data->pph_price), 0, '.', '.') ?></b><br>
                            PPN dan PPh akan disetorkan oleh Bendahara<br>
                            <b>(Pembelian menggunakan mekanisme Pembayaran Langsung)</b><br>
                            <!-- <b>Rp <?= number_format((float) $data->total, 0, '.', '.') ?></b> -->
                        </p>
                        <?php } else { ?>
                        <p class="text-right" style="font-family: helvetica; font-size: 12px;">
                            Sesuai PMK Nomor 58/PMK.03/2022<br />
                            Nilai yang harus dibayarkan <b>Rp
                                <?= number_format((float) $data->total, 0, '.', '.') ?></b><br />
                            PPN dan PPh akan disetorkan oleh<br />
                            PT. Elite Proxy Sistem<br />
                            (Pihak Lain yang ditunjuk sebagai Pemungut Pajak)
                        </p>
                        <?php } ?>

                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
    } else { ?>
    <div class="row" style="margin-bottom: 0px;">
        <p style="font-family: helveticab; margin-bottom:5px;"></p>
        <div class="col-xs-12">
            <div style="font-family: helvetica; font-size: 12px;">
                <p>
                    *Invoice ini berlaku sebagai Bukti Pungut PPh Pasal 22 dan dokumen tertentu
                    yang kedudukannya dipersamakan dengan Faktur Pajak
                </p>
            </div>
        </div>
        <div class="col-xs-7">
        </div>

    </div>
    <div class="row" style="margin-top: 0px;">
        <div class="col-xs-12">
            <div style="font-family: helvetica; font-size: 12px;">
                <p>
                    **Transaksi ini telah dikenakan pph pasal 22 sebesar <?= $data->val_pph ?>% ke penyedia dan
                    mitra pengiriman dari nilai invoice diluar PPN

                </p>
            </div>
        </div>
    </div>
    <?php } ?>
</body>
