<!DOCTYPE html>
<html>

<head>
    <title>Informasi Pengiriman</title>
</head>

<body style="width:65%; height:45%; ">
    <table width="100%" style="border: 1px solid grey; padding: 10px; border-radius: 8px; font-size: 17px;"
        cellpadding="5">
        <tr style="background-color:grey !important;">
            <td colspan="2" align="center">
                <img src="{{ public_path('img/app/logo-eps.png') }}" width="25%">
            </td>
        </tr>
        <tr>
            <td width="50%">
                <h3 style="margin: 0px !important">No. Pesanan :<br><?= $data->invoice . '-' . $data->id_cart_shop ?>
                </h3>
            </td>
            <td width="50%">
                <h3 style="margin: 0px !important"><?= strtoupper($data->courier_code) . ' - ' . $data->service ?>
                    <!--<br> No. Resi : ...........................................</h3>-->
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h3 style="margin: 0px !important">No. Resi / kode booking :</h3>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <h3 style="margin: 0px !important"><?= $data->no_resi ?></h3>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <hr>
            </td>
        </tr>
        <tr>
            <td><b>Penerima :<?= $data->nama ?></b></td>
            <td><b>Pengirim :<?= $data->nama_seller ?></b></td>
        </tr>
        <tr>
            <td>
                <span class="black-text"><b>(<?= $data->address_name . ') ' . $data->phone ?>) - </b></span>
                <?= $data->address . ', ' . strtoupper($data->city_name . ' - ' . $data->subdistrict_name . ', ' . $data->province_name . ', ID ' . $data->postal_code) ?>
            </td>
            <td>
                    @foreach ($data->seller_address as $sla)
                <span class="black-text"><b>(<?= $sla->phone ?>) - </b></span>
                <?= $sla->address . ', ' . strtoupper($sla->city_name . ' - ' . $sla->subdistrict_name . ', ' . $sla->province_name . ', ID ' . $sla->postal_code) ?>
                @endforeach
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>Berat :</b><?= str_replace(',', '.', number_format($data->total_weight)) ?> gr<br>
                <b>Biaya Pengiriman :</b><?= str_replace(',', '.', number_format($data->sum_shipping)) ?><br>
                <b>Catatan :<i><?= $data->note ?></i></b>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>Nama Produk</b>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <hr>
            </td>
        </tr>
        <?php $no=1 ?>
        @foreach ($data->detail as $product)
            <tr>
                <td colspan="2">
                    <!-- Tampilkan nomor urut, kemudian tambahkan 1 ke variabel $no -->
                    {{ $no++ }}. <b>{{ str_replace(',', '.', number_format($product->qty_produk)) }}x</b>
                    <?= $product->nama_produk ?>
                </td>
            </tr>
        @endforeach
    </table>
</body>

</html>
