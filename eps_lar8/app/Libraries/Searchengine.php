<?php

namespace App\Libraries;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Searchengine
{
    function quickSearch($query)
    {
        $categories = DB::table('product_category')
            ->where('name', 'like', "%$query%")
            ->where('active', 'Y')
            ->take(5)
            ->get();

        $productsearch = DB::table('products')
            ->select(
                'products.*',
                DB::raw('(SELECT image50 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image50')
            )
            ->join('shop', 'products.id_shop', '=', 'shop.id')
            ->where('products.name', 'like', "%$query%")
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('shop.status', 'active')
            ->take(5)
            ->get();

        $stores = DB::table('shop')
            ->where('name', 'like', "%$query%")
            ->where('status', 'active')
            ->take(5)
            ->get();

        return compact('categories', 'productsearch', 'stores');
    }

    function fullSearch($query = null, $where = null, $category = null, $is_lpse, $perPage = 20, $page = 1)
    {
        $keyword = $query;
        $stores = null;

        if ($query != null) {
            $categories = DB::table('product_category as pc1')
                ->select('pc1.id', 'pc1.name')
                ->join('product_category as pc2', 'pc2.parent_id', '=', 'pc1.id')
                ->join('products', 'products.id_category', '=', 'pc2.id')
                ->where('products.name', 'like', "%$query%")
                ->where('pc1.level', 1)
                ->where('pc1.active', 'Y')
                ->where('pc1.display_status', 'show')
                ->where('pc1.parent_id', 0)
                ->distinct()
                ->get();

            $menuCategories = [];

            foreach ($categories as $cat) {
                $subMenus = DB::table('product_category')
                    ->select('id', 'name')
                    ->where('parent_id', $cat->id)
                    ->where('active', 'Y')
                    ->get();

                $menuCategories[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'submenus' => $subMenus
                ];
            }

            $productsearch = DB::table('products')
                ->select(
                    'products.*',
                    'lp.price_lpse as hargaTayang',
                    's.name as namaToko',
                    's.id as idToko',
                    'p.province_name',
                    DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image300')
                )
                ->leftJoin('lpse_price as lp', 'products.id', '=', 'lp.id_product')
                ->join('shop as s', 'products.id_shop', '=', 's.id')
                ->leftJoin('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
                ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id')
                ->where('products.name', 'like', "%$query%")
                ->where('products.status_display', 'Y')
                ->where('products.status_delete', 'N')
                ->where('s.status', 'active');

            if ($is_lpse == 1) {
                $productsearch->where('s.is_lpse_verified', 1)
                    ->where('products.status_lpse', 1);
            }

            if ($where) {
                foreach ($where as $key => $value) {
                    $productsearch->where($key, $value);
                }
            }

            $productsearch = $productsearch->paginate($perPage, ['*'], 'page', $page);

            // Debugging
            Log::info('Type of $productsearch: ' . get_class($productsearch));
            Log::info('$productsearch contents: ' . json_encode($productsearch));

            $stores = DB::table('shop')
                ->where('name', 'like', "%$query%")
                ->where('status', 'active')
                ->limit(3)
                ->get();
        } else {
            if ($category != null) {
                // Ambil kategori yang dipilih
                $selectedCategory = DB::table('product_category')
                    ->where('code', $category)
                    ->where('icon', '!=', null)
                    ->where('lpse_report_id', '!=', null)
                    ->where('active', 'Y')
                    ->where('display_status', 'show')
                    ->first();

                if ($selectedCategory) {
                    // Ambil submenu dari kategori yang dipilih
                    $subMenus = DB::table('product_category')
                        ->where('parent_id', $selectedCategory->id)
                        ->where('active', 'Y')
                        ->get();

                    $menuCategories[] =
                        [
                            'id' => $selectedCategory->id,
                            'name' => $selectedCategory->name,
                            'submenus' => $subMenus
                        ];
                } else {
                    $menuCategories = [];
                }

                $productsearch = DB::table('products')
                    ->select(
                        'products.*',
                        'lp.price_lpse as hargaTayang',
                        's.name as namaToko',
                        's.id as idToko',
                        'p.province_name',
                        DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image300')
                    )
                    ->leftJoin('lpse_price as lp', 'products.id', '=', 'lp.id_product')
                    ->join('shop as s', 'products.id_shop', '=', 's.id')
                    ->leftJoin('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
                    ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id')
                    ->leftJoin('product_category as pc', 'products.id_category', '=', 'pc.id')
                    ->where('products.status_display', 'Y')
                    ->where('products.status_delete', 'N')
                    ->where('s.status', 'active');

                // Tambahkan kondisi untuk status_lpse
                if ($is_lpse == 1) {
                    $productsearch->where('s.is_lpse_verified', 1)
                        ->where('products.status_lpse', 1); // Pastikan status_lpse adalah 1
                }

                // Gabungkan kondisi untuk kategori
                $productsearch->where(function ($query) use ($selectedCategory) {
                    $query->where('products.id_category', $selectedCategory->id)
                        ->orWhere('pc.parent_id', $selectedCategory->id);
                });

                if ($where) {
                    foreach ($where as $key => $value) {
                        $productsearch->where($key, $value);
                    }
                }

                $productsearch = $productsearch->paginate($perPage, ['*'], 'page', $page);
            } else {
                // Kode yang sudah ada untuk menampilkan semua kategori
                $categories = DB::table('product_category')
                    ->where('active', 'Y')
                    ->where('display_status', 'show')
                    ->where('parent_id', 0)
                    ->Where('level', 1)
                    ->get();

                $menuCategories = [];
                $subMenuCategories = [];

                foreach ($categories as $cat) {
                    $subMenus = DB::table('product_category')
                        ->where('parent_id', $cat->id)
                        ->where('active', 'Y')
                        ->get();

                    $menuCategories[$cat->id] = [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'submenus' => [$subMenus]
                    ];
                }

                $menuCategories = array_values($menuCategories);
            }
        }

        return compact('menuCategories', 'productsearch', 'stores', 'keyword');
    }

    public function filterSearching($data, $is_lpse)
    {
        $query = DB::table('products')
            ->select(
                'products.*',
                'shop.name as shop_name',
                'p.province_name',
                'lp.price_lpse as hargaTayang',
                DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image')
            )
            ->join('shop', 'products.id_shop', '=', 'shop.id')
            ->leftJoin('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->leftJoin('member_address as ma', 'shop.id_address', '=', 'ma.member_address_id')
            ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id')
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('shop.status', 'active');

        // Perbaikan untuk kondisi is_lpse
        if ($is_lpse == 1) {
            $query->where('shop.is_lpse_verified', 1)
                ->where('products.status_lpse', 1); // Hanya ambil produk dengan status_lpse = 1
        } else {
            $query->where('products.status_lpse', 0); // Jika tidak, ambil produk dengan status_lpse = 0
        }

        // Filter berdasarkan id_shop jika ada
        if (!empty($data['idshop'])) {
            $query->where('products.id_shop', $data['idshop']);
        }

        // Filter lainnya
        if (!empty($data['sort'])) {
            switch ($data['sort']) {
                case 'terbaru':
                    $query->orderBy('products.created_at', 'desc');
                    break;
                case 'h_tertinggi':
                    $query->orderBy('lp.price_lpse', 'desc');
                    break;
                case 'terjual':
                    $query->orderBy('products.count_sold', 'desc');
                    break;
                case 'h_terendah':
                    $query->orderBy('lp.price_lpse', 'asc');
                    break;
            }
        }

        if (!empty($data['keyword'])) {
            $query->where('products.name', 'like', '%' . $data['keyword'] . '%');
        }

        if (!empty($data['category'])) {
            $query->leftJoin('product_category as pc', 'products.id_category', '=', 'pc.id');
            $query->where(function ($q) use ($data) {
                $q->where('products.id_category', $data['category'])
                    ->orWhere('pc.parent_id', $data['category']);
            });
        }

        if (!empty($data['min'])) {
            $query->where('lp.price_lpse', '>=', $data['min']);
        }

        if (!empty($data['max'])) {
            $query->where('lp.price_lpse', '<=', $data['max']);
        }

        if (!empty($data['condition'])) {
            $query->where('products.status_new_product', $data['condition']);
        }

        // Mengambil hasil pencarian
        $productsearch = $query->get();

        // print_r($productsearch);
        // exit();
        return $productsearch;
    }

    function SaveLogSearch($keyword, $id_product = null, $id_user = null, $count_result = null)
    {
        $check = DB::table('log_search')
            ->where('keyword', $keyword)
            ->first();

        if ($check) {
            DB::table('log_search')
                ->where('id', $check->id)
                ->update([
                    'count_result' => $count_result,
                    'search_count' => $check->search_count + 1
                ]);
        } else {
            DB::table('log_search')->insert([
                'keyword' => $keyword,
                'id_user' => $id_user,
                'first_product' => $id_product,
                'count_result' => $count_result,
                'search_count' => 1,
                'created_dt' => now()
            ]);
        }
    }
}
