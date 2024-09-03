<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">
        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="box-home" style=" background-color:#EFF9FF">
                            <h4 class="page-header" style="margin: 2px">
                                Informasi Toko tentang
                                <small>Ini merupakan notifikasi tentang toko mu</small>
                            </h4>
                        </div>
                        <div class="box-home" style=" background-color:#EFF9FF; display:grid">
                            <div class="box box-info"
                                style="background-color: white; border: 2px solid #FC6703; border-radius: 10px; box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.15); padding: 16px; margin-bottom: 20px;">
                                <b>Semua Notifikasi</b>
                                <hr>
                                <?php
                                function getImageUrl($image) {
                                    return strpos($image, 'http') === false ? 'https://eliteproxy.co.id/' . $image : $image;
                                }

                                function displayListItem($image, $name, $text, $date) {
                                    $imageUrl = getImageUrl($image);
                                    $date_only = substr($date, 0, 10);
                                    echo "
                                    <li style='border-bottom: 1px solid #ccc; padding: 10px 0; display: flex; align-items: center; box-sizing: border-box;'>
                                        <img id='only-dekstop' src='$imageUrl' alt='Product Image' style='width: 50px; height: 50px; margin-right: 15px; border-radius: 5px;'>
                                        <div style='flex: 1;'>
                                            <p style='margin: 0; font-weight: bold;'>$name</p>
                                            <p style='margin: 0;'>$text</p>
                                            <p style='font-size: xx-small; margin: 0;'>$date_only</p>
                                        </div>
                                    </li>
                                    ";
                                }

                                function displayOrderItem($invoice, $status, $date) {
                                    $date_only = substr($date, 0, 10);
                                    echo "
                                    <li style='border-bottom: 1px solid #ccc; padding: 10px 0; display: flex; align-items: center; width: 100%; box-sizing: border-box;'>
                                        <i class='material-icons' id='only-dekstop' style='font-size: 50px; margin-right: 15px; color: #FC6703;'>description</i>
                                        <div style='flex: 1;'>
                                            <p style='margin: 0; font-weight: bold;'>$invoice</p>
                                            <p style='margin: 0;'>Pembaharuan Status <b>$status</b></p>
                                            <p style='font-size: xx-small; margin: 0;'>$date_only</p>
                                        </div>
                                    </li>
                                    ";
                                }

                                $all_notifications = [];

                                foreach ($notif_productReviews as $productReview) {
                                    $all_notifications[] = [
                                        'type' => 'productReview',
                                        'image' => $productReview->image_product,
                                        'name' => $productReview->nama_member,
                                        'text' => "memfavoritkan produk <b>{$productReview->name}</b>",
                                        'date' => $productReview->created_dt
                                    ];
                                }

                                foreach ($notif_favorites as $productfavorite) {
                                    $all_notifications[] = [
                                        'type' => 'favorite',
                                        'image' => $productfavorite->image_product,
                                        'name' => $productfavorite->nama_member,
                                        'text' => "memfavoritkan produk <b>{$productfavorite->name}</b>",
                                        'date' => $productfavorite->created_dt
                                    ];
                                }

                                foreach ($notif_orders as $productorder) {
                                    $all_notifications[] = [
                                        'type' => 'order',
                                        'invoice' => $productorder->invoice,
                                        'status' => $productorder->status,
                                        'date' => $productorder->last_update
                                    ];
                                }

                                foreach ($notif_promos as $productpromo) {
                                    $all_notifications[] = [
                                        'type' => 'promo',
                                        'image' => $productpromo->image_product,
                                        'name' => $productpromo->name,
                                        'text' => "Promo produk ini telah aktif",
                                        'date' => $productpromo->created_dt
                                    ];
                                }

                                // Urutkan notifikasi berdasarkan tanggal terbaru
                                usort($all_notifications, function ($a, $b) {
                                    return strtotime($b['date']) - strtotime($a['date']);
                                });
                                ?>

                                <ul style="list-style: none; padding: 0;">
                                    <?php
                                    foreach ($all_notifications as $notification) {
                                        if ($notification['type'] === 'order') {
                                            displayOrderItem($notification['invoice'], $notification['status'], $notification['date']);
                                        } else {
                                            displayListItem($notification['image'], $notification['name'], $notification['text'], $notification['date']);
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div>
    </div>
</body>
<script src="{{ asset('/js/function/seller/dashboard.js') }}" type="text/javascript"></script>

@include('seller.asset.footer')

</html>
