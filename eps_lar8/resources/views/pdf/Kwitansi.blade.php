<!DOCTYPE html>
<html lang="en">
<head>
    <title>Kwitansi</title>
    <style>
        body {
            font-family: 'Roboto', 'sans-serif';
            font-size: 16px;
            margin: 0;
            padding: 0;
        }

        .kwitansi-table {
            width: 100%;
            border-spacing: 0;
            border-radius: 10px; /* Sudut luar melengkung */
            border: 1px solid orange;
        }

        .kwitansi-table tr:not(:last-child) {
            border-bottom: 1px solid orange;
        }

        .kwitansi-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid orange;
        }

        .total-bayar{
            border-radius: 10px;
            border: 1px solid orange;
            width: 40%;
            font-size: 20px;
            text-align: center;
            height: 60px;
        }

        .left {
            width: 70%;
            float: left;
            text-align: left;
            margin-right: 5px;
        }

        .right {
            width: 30%; /* Lebar elemen */
            float: right; /* Mengatur agar elemen berada di sebelah kanan */
            margin-left: 5px; /* Margin kiri untuk memberi jarak dari elemen lain */
            box-sizing: border-box; /* Menyertakan padding dalam perhitungan lebar elemen */
            font-size: 12px
        }

    </style>
</head>
<body>

    <div class="logo">
        <img src='{{ public_path('img/app/logo-eps-crop.png') }}' width='30%'>
    </div>
    <br>
    <table style="width: 100%; font-size:14px">
        <tr>
            <th style="width: 70%;text-align:left">KWITANSI</th>
            <th style="text-align:left">Nomor Kwitansi</th>
        </tr>
        <tr>
            <td>
                PT.Elite Proxy Sistem <br>
                Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15
                Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta
            </td>
            <td>
                KW/ {{$data->invoice . '-' . $data->id_cart_shop }}
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table class="kwitansi-table">
        <tr>
            <td style="width:30%; text-align:center;"> <b> Sudah Terima dari </b></td>
            <td style="border-left: 1px solid orange;">{{$dataPembeli->instansi}}, {{$dataPembeli->satker}}</td>
        </tr>
        <tr>
            <td style="width:30%; text-align:center;"> <b> Terbilang </b></td>
            <td style="border-left: 1px solid orange;"> {{$data->terbilang}} </td>
        </tr>
        <tr>
            <td style="width:30%; border-bottom: 0px; text-align:center;"> <b> Untuk Pembayaran </b> </td>
            <td style="border-left: 1px solid orange; border-bottom: 0px"> Kegiatan Jual Beli melalui Mitra Toko Daring EliteProxy.co.id </td>
        </tr>
    </table>
    <br>
    <div class="total-bayar">
        <p style="margin-top:15px"><b> Rp. {{number_format((float) $data->total, 0, '.', '.')}} </b></p>
    </div>
    <div>

        @if ($data->id_payment == 23)
            <p>
                <b>Catatan</b> <br>
                1. Mohon pembayaran dilakukan melalui transfer ke rekening bank berikut ini :
                <b> BNI Cabang Sudirman Park </b>
                A/N : <b> PT. Elite Proxy Sistem </b>
                No : <b> 03975-60583 </b>
            </p>
            @if ($data->province_name == 'Banten')
                <p>
                    <b> Bank Banten KC Kelapa Gading </b>
                    A/N : <b> PT. Elite Proxy Sistem </b>
                    No : <b> 0302010447 </b>
                </p>
            @elseif ($data->province_name == 'DI Yogyakarta')
                <p>
                    <b> BPD DIY Cabang Yogyakarta </b>
                    A/N : <b> PT. Elite Proxy Sistem </b>
                    No : <b> 001111002037 </b>
                </p>
            @endif
            <p>
                2. Pembayaran dianggap sah setelah nominal tersebut sudah masuk ke rekening diatas
            </p>
        @elseif ($data->id_payment == 30 || 31)
            <p>
                <b>Catatan</b> <br>
                1. Mohon lakukan pembayaran melalui virtual account berikut ini : <br>
                &nbsp; Nomor Virtual Account <b> {{$data->va_number}}</b> <br>
            </p>
        @endif
    </div>

    <div class="left">
        &nbsp;
    </div>
    <div class="right">
        <p>
            DKI Jakarta, 12 Juli 2024 <br>
            Hormat kami, <br>
            <b> PT.Elite Proxy Sistem </b>
        </p>
    </div>
</body>
</html>
