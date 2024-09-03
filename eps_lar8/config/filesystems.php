<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'products' => [
            'driver' => 'local',
            'root' => storage_path('app/public/products'),
            'url' => env('APP_URL').'/storage/products',
            'visibility' => 'public',
        ],

        'file_DO' => [
            'driver' => 'local',
            'root' => storage_path('app/public/file_DO'),
            'url' => env('APP_URL').'/storage/file_DO',
            'visibility' => 'public',
        ],

        'upload_payment' => [
            'driver' => 'local',
            'root' => storage_path('app/public/payments'),
            'url' => env('APP_URL').'/storage/payments',
            'visibility' => 'public',
        ],

        'akta_perubahan' => [
            'driver' => 'local',
            'root' => storage_path('app/public/akta_perubahan'),
            'url' => env('APP_URL').'/storage/akta_perubahan',
            'visibility' => 'public',
        ],

        'akta' => [
            'driver' => 'local',
            'root' => storage_path('app/public/akta_perubahan'),
            'url' => env('APP_URL').'/storage/akta_perubahan',
            'visibility' => 'public',
        ],

        'akta_pendirian' => [
            'driver' => 'local',
            'root' => storage_path('app/public/akta_pendirian'),
            'url' => env('APP_URL').'/storage/akta_pendirian',
            'visibility' => 'public',
        ],

        'npwp' => [
            'driver' => 'local',
            'root' => storage_path('app/public/npwp'),
            'url' => env('APP_URL').'/storage/npwp',
            'visibility' => 'public',
        ],

        'pkp' => [
            'driver' => 'local',
            'root' => storage_path('app/public/pkp'),
            'url' => env('APP_URL').'/storage/pkp',
            'visibility' => 'public',
        ],

        'ktp' => [
            'driver' => 'local',
            'root' => storage_path('app/public/ktp'),
            'url' => env('APP_URL').'/storage/ktp',
            'visibility' => 'public',
        ],

        'nib' => [
            'driver' => 'local',
            'root' => storage_path('app/public/nib'),
            'url' => env('APP_URL').'/storage/nib',
            'visibility' => 'public',
        ],

        'avatar' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatar'),
            'url' => env('APP_URL').'/storage/avatar',
            'visibility' => 'public',
        ],

        'shop_banner' => [
            'driver' => 'local',
            'root' => storage_path('app/public/shop_banner'),
            'url' => env('APP_URL').'/storage/shop_banner',
            'visibility' => 'public',
        ],

        'vidio_product' => [
            'driver' => 'local',
            'root' => storage_path('app/public/vidio_product'),
            'url' => env('APP_URL').'/storage/vidio_product',
            'visibility' => 'public',
        ],

        'faktur' => [
            'driver' => 'local',
            'root' => storage_path('app/public/faktur'),
            'url' => env('APP_URL').'/storage/faktur',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],

        'products' => [
            'driver' => 'local',
            'root' => storage_path('app/public/products'),
            'url' => env('APP_URL').'/storage/products',
            'visibility' => 'public',
        ],

        'assets' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL'),
            'visibility' => 'public',
        ],

        'file_DO' => [
            'driver' => 'local',
            'root' => storage_path('app/public/file_DO'),
            'url' => env('APP_URL').'/storage/file_DO',
            'visibility' => 'public',
        ],

        'upload_payment' => [
            'driver' => 'local',
            'root' => storage_path('app/public/payments'),
            'url' => env('APP_URL').'/storage/payments',
            'visibility' => 'public',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
