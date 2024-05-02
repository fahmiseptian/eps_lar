<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Kwitansi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9FzXvAqOXIOIjeBfO5w2MndYv2k3iN1LfeJr1G3E7AGTLlZ45Fgtx0e4h4n0U7fF" crossorigin="anonymous">

    <style>
        /* Import font styles */
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

        /* Table styling */
        table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        table tr th,
        table tr td {
            border-right: 1px solid #fc6703 !important;
            border-bottom: 1px solid #fc6703 !important;
        }

        table tr th:first-child,
        table tr td:first-child {
            border-left: 1px solid #fc6703 !important;
        }

        table tr th {
            border-top: 1px solid #fc6703 !important;
        }

        /* Border radius for table corners */
        table tr:first-child th:first-child {
            border-top-left-radius: 1rem !important;
        }

        table tr:first-child th:last-child {
            border-top-right-radius: 1rem !important;
        }

        table tr:last-child td:first-child {
            border-bottom-left-radius: 1rem !important;
        }

        table tr:last-child td:last-child {
            border-bottom-right-radius: 1rem !important;
        }

        /* Styling for table headers */
        table th {
            font-weight: normal;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Header row -->
        <div class="row">
            <div class="col-xs-4">
                <img src='{{ public_path('img/app/logo-eps-crop.png') }}' width='40%'>
            </div>
        </div>
        <br />

        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <div style="width: 60%;">
                <div>KWITANSI</div>
                <div>
                    <?= $eps['nama'] ?> <br />
                    <?= $eps['alamat'] ?>
                </div>
            </div>
            <div style="margin-top:-30%; text-align: right; margin-bottom: 50px">
                <div>Nomor Kwitansi</div>
                <div>KW/<?= $data->invoice . '-' . $data->id_cart_shop ?></div>
            </div>
        </div>
        
        <br />
        
        
        <br />
        
        <!-- Tabel informasi -->
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered" style="width: 100%; border-radius: 4px">
                    <thead>
                        <tr>
                            <th scope="col" style="vertical-align: middle; font-family: helveticab;" class="text-center">Sudah terima dari</th>
                            <th scope="col" width="75%"><?= $data->instansi . ' ' . $data->satker ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row" style="vertical-align: middle; font-family: helveticab;" class="text-center">Terbilang</td>
                            <td>{{ $data->terbilang }} Rupiah</td>
                        </tr>
                        <tr>
                            <td scope="row" style="vertical-align: middle; font-family: helveticab;" class="text-center">Untuk Pembayaran</td>
                            <td>Kegiatan Jual Beli melalui Mitra Toko Daring EliteProxy.co.id</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        

        <!-- Payment amount and other details -->
        <div class="row">
            <div class="col-xs-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="vertical-align: middle; border-bottom-left-radius: 1rem !important; border-bottom-right-radius: 1rem !important; font-family: helveticab; font-size:22px;" class="text-center">
                                Rp. <?= number_format((float) $data->total, 0, '.', '.') ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div style="font-family: helvetica;">
                    <!-- Notes for payment methods -->
                    <?php if (in_array($data->id_payment, array(23, 30))) { ?>
                        <p style="font-family: helveticab; margin-bottom:5px;">Catatan</p>
                        <p>
                            <?php if (in_array($data->id_payment, array(30))) { ?>
                                1. Mohon lakukan pembayaran melalui virtual account BCA berikut ini:
                                <br /><br />
                                Virtual Account BCA <b>{{ $data->va_number }} </b>
                            <?php } ?>
                            <br />

                            <?php if (in_array($data->id_payment, array(23))) { ?>
                                1. Mohon pembayaran dilakukan melalui transfer ke rekening bank berikut ini:
                                <br />
                                <table width="100%">
                                    <td width="50%" class="px-0 mx-0">
                                        <b>BNI Cabang Sudirman Park</b><br />
                                        A/N: <b>PT. Elite Proxy Sistem</b><br />
                                        No: <b>03975-60583</b><br />
                                    </td>
                                    <?php if ($data->seller_address[0]['province_name'] == 'Banten') { ?>
                                        <td width="50%" class="px-0 mx-0">
                                            <b>Bank Banten KC Kelapa Gading</b><br />
                                            A/N: <b>PT. Elite Proxy Sistem</b><br />
                                            No: <b>0302010447</b><br />
                                        </td>
                                    <?php } ?>
                                    <?php if ($data->seller_address[0]['province_name'] == 'DI Yogyakarta') { ?>
                                        <td width="50%" class="px-0 mx-0">
                                            <b>BPD DIY Cabang Yogyakarta</b><br />
                                            A/N: <b>PT. ELITE PROXY SISTEM</b><br />
                                            No: <b>001111002037</b><br />
                                        </td>
                                    <?php } ?>
                                </table>
                                <br />
                                2. Pembayaran dianggap sah setelah nominal tersebut sudah masuk ke rekening di atas.
                            <?php } ?>
                        </p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <br />

        <!-- Signature and location -->
        <div class="row">
            <div class="col-xs-5">
                <div class="text-left" style="float: right; width:30%">
                    <?= $data->seller_address[0]['province_name'] . ', ' . $data->tgl_indo ?>
                    <br />
                    <p>Hormat kami,</p>
                    <p style="font-family: helveticab; margin-bottom:5px;"><?= $eps['nama'] ?></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
