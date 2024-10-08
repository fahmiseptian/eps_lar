<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'is3ds' => env('MIDTRANS_IS_3DS', true),
    'isSanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://api.sandbox.midtrans.com/'),
    'url_payment' => env('MIDTRANS_SNAP_PAYMENT_URL', 'https://app.sandbox.midtrans.com/')
];
