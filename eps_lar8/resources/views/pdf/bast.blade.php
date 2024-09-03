<?php

function numberFormatID($id)
{
    return sprintf('%06d', $id);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <?php
    $proforma = false;
    if (in_array($data->id_payment, [23, 24, 25])) {
        if ($data->status_pembayaran_top == '0') {
            $proforma = true;
        }
    } elseif ($data->id_payment == 22) {
        // NOTE Kartu Kredit
        if ($data->invoice_status != 'complete_payment' && $data->invoice_status != 'completed') {
            $proforma = true;
        }
    }
    if (empty($data->nama_pemilik)) {
        $data->nama_pemilik = $data->sellername;
    }
    $no_bast = numberFormatID($data->id_cart_shop) . '/LO/ST/' . date('m/Y', strtotime($data->created_date));

    ?>
    <meta charset="utf-8" />
    <title>B.A.S.T</title>
    <link rel="stylesheet" type="text/css" href="<?php echo 'assets/css/css/bootstrap.min.css'; ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo 'assets/css/css/bootstrap-theme.min.css'; ?>" />
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

            border: 1px solid #fc6703;

        }



        table.table-bordered thead th {

            border-top: none;

            border-bottom: 1px solid #fc6703;

            border-left: 1px solid #fc6703 !important;

            border-right: 1px solid #fc6703 !important;

        }



        table.table-bordered td {

            border-left: 1px solid #fc6703 !important;

            border-right: 1px solid #fc6703 !important;

            border-top: none !important;

            border-bottom: none !important;

        }
    </style>
</head>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-8">
            <div style="font-family: helvetica; border-left: 5px solid #fc6703 !important; padding-left:10px;">
                <p style="font-family: helveticab; margin-bottom:5px;">Berita Acara Serah Terima</p>
                <table border="0">
                    <tbody>
                        <tr>
                            <td>
                                No. <?= $proforma ? 'Proforma' : 'Invoice' ?>
                            </td>
                            <td>&nbsp; : &nbsp;</td>
                            <td>{{ ($proforma ? 'PRF' : '') . $data->invoice . '-' . $data->id_cart_shop }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                No. BAST
                            </td>
                            <td>&nbsp; : &nbsp;</td>
                            <td><?= $no_bast ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-4" style="text-align: right; margin-top:-60px">
            <img src='{{ public_path('img/app/logo-eps-crop.png') }}' width="20%">
        </div>
    </div>
    <br>
    <div class="row">
        <h3 class="center-align center center-text">
            <center>BERITA ACARA SERAH TERIMA</center>
        </h3>
        <h4 class="center-align center-text" style="margin-top: -15px">
            <center>Nomor: <?= $no_bast ?></center>
        </h4>
    </div>

    <?php
    function getHariIndo($day)
    {
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        return $hari[$day] ?? null;
    }

    function getBulanIndo($month)
    {
        $bulan = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];
        return $bulan[$month] ?? null;
    }
    $created_date = $bast->detail->created_date;

    // Mengambil tanggal, hari, dan bulan dari created_date
    $tgl = date('d', strtotime($created_date));
    $day = date('l', strtotime($created_date));
    $month = date('F', strtotime($created_date));
    $tahun = date('Y', strtotime($created_date));

    // Mengonversi nama hari dan bulan ke bahasa Indonesia
    $nama_hari = getHariIndo($day);
    $nama_bulan = getBulanIndo($month);

    // Menampilkan tanggal dalam format bahasa Indonesia
    echo "Pada hari ini, $nama_hari, tanggal $tgl $nama_bulan $tahun, sesuai dengan :";
    ?>

    <br>
    <br>
    <table cellspacing="3" border="0">
        <tr>
            <td>Nomor surat perjanjian</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $no_bast }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ date('d/m/y', strtotime($bast->detail->created_date)) }}</td>
        </tr>
        <tr>
            <td>Untuk Keperluan</td>
            <td>&nbsp; : &nbsp; </td>
            <td><?= $data->keperluan ?: 'Kegiatan Jual - Beli melalui eliteproxy.co.id' ?></td>
        </tr>
        <tr>
            <td>Tahun</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ date('Y', strtotime($bast->detail->created_date)) }}</td>
        </tr>
    </table>
    <p>Yang bertandatangan di bawah ini:</p>
    <table>
        {{-- Penjual --}}
        <tr>
            @foreach ($data->seller_address as $sla)
                <td rowspan="4" valign="top">1. &nbsp;</td>
                <td>Nama Perushaan</td>
                <td>&nbsp; : &nbsp; </td>
                <td> {{ $sla->nama_pt }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $sla->nama_pemilik }}</td>
        </tr>
        <tr>
            <td>Alamat Perusahaan</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $sla->address }}</td>
        </tr>
        <tr>
            <td>NPWP</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $sla->npwp }}</td>
        </tr>
    </table>
    <p>Sebagai pihak yang menyerahkan, selanjutnya disebut PIHAK PERTAMA</p>
    {{-- Pembeli --}}
    <table>
        <tr>
            <td rowspan="6" valign="top">2. &nbsp;</td>
            <td>Nama</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $data->nama }}</td>
        </tr>
        <tr>
            <td>Nama Instansi </td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $data->instansi }}</td>
        </tr>
        <tr>
            <td>Satuan Kerja</td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $data->satker }}</td>
        </tr>
        <tr>
            <td>Alamat Pengiriman </td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $data->address }},{{ $data->city }}-{{ $data->subdistrict_name }},{{ $data->province_name }}
                {{ $data->postal_code }}</td>
        </tr>
        <tr>
            <td>No. Telepon </td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $data->phone }}</td>
        </tr>
        <tr>
            <td>NPWP </td>
            <td>&nbsp; : &nbsp; </td>
            <td>{{ $data->npwp }}</td>
        </tr>
    </table>
    <p>Sebagai pihak yang menerima, selanjutnya disebut PIHAK KEDUA </p>

    <p>
        PIHAK PERTAMA menyerahkan hasil pekerjaan pengiriman barang atas kegiatan jual-beli melalui Mitra Toko
        daring eliteproxy.co.id kepada PIHAK KEDUA, dan PIHAK KEDUA telah menerima hasil pekerjaan tersebut
        dalam jumlah dan kondisi yang sesuai dengan rincian berikut :
    </p>

    <table class="table-bordered"
        style="width: 100%; background: url('{{ public_path('img/app/inv-logo-eps.png') }}')">
        <thead>
            <tr>
                <th scope="col" width="5%">No.</th>
                <th scope="col" width="50%">Deskripsi</th>
                <th scope="col" width="9%">Unit</th>
                <th scope="col" width="12%">Jumlah Dipesan</th>
                <th scope="col" width="12%">Jumlah Diterima</th>
                <th scope="col" width="12%">Jumlah Rusak/Dikembalikan</th>
            </tr>
        </thead>
        <?php
        // Inisialisasi variabel nomor urut
        $no = 1;
        ?>

        <tbody>
            @foreach ($bast->detail->details as $index => $pr)
                <?php
                $relativePath = str_replace(url('/storage'), 'public', $pr->gambar);
                $filePath = storage_path('app/' . $relativePath);
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td style="height:70px"><img src="{{ $filePath }}" width='50px'> {{ $pr->name }}</td>
                    <td>Unit</td>
                    @if (isset($data_qty[$index]))
                        <td>{{ $data_qty[$index]->qty }}</td>
                        <td>{{ $data_qty[$index]->qty_diterima }}</td>
                        <td>{{ $data_qty[$index]->qty_dikembalikan }}</td>
                    @else
                        <td>Data Tidak Tersedia</td>
                        <td>Data Tidak Tersedia</td>
                        <td>Data Tidak Tersedia</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-xs-12">
            <div style="font-family: helvetica;">
                <p style="font-size: 12px;">
                    Berita Acara Serah Terima ini berfungsi sebagai bukti serah terima barang serta menentukan jumlah
                    yang perlu dibayar berdasarkan QTY yang diterima dari masing-masing barang yang selanjutnya akan
                    dicatat pada penerimaan barang.
                </p>
                <p style="font-size: 12px;">
                    Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana
                    seharusnya.
                </p>
                <p style="font-size: 12px;">
                    Ini adalah dokumen yang terbuat secara otomatis oleh komputer. Tidak ada tanda tangan yang
                    diperlukan.
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <br>
        <div class="col-xs-12">
            <table style="width: 100%;" border="0">
                <tbody>
                    <tr>
                        <td style="text-align: center; vertical-align: top; width: 40%;">Pihak Pertama</td>
                        <td style="height: 50px; width:10%;">&nbsp;</td>
                        <td style="text-align: center; vertical-align: top; width: 40%;">Pihak Kedua</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td style="height: 50px; width:10%;">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; vertical-align: bottom; width: 40%;">
                            <span style="text-decoration: underline;"><?= $sla->nama_pemilik ?></span>
                        </td>
                        <td style="height: 50px; width:10%;">&nbsp;</td>
                        <td style="text-align: center; vertical-align: bottom; width: 40%;">
                            <span style="text-decoration: underline;"><?= $data->nama ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; vertical-align: top; width: 40%;">
                            <?= $sla->nama_pt ?>
                        </td>
                        <td style="height: 50px; width:10%;">&nbsp;</td>
                        <td style="text-align: center; vertical-align: top; width: 40%;">
                            <?= $data->satker ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

</html>
