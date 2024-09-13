<?php

namespace App\Libraries;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class Searchengine
{
    function quickSearch($query)
    {
        $categories = DB::table('product_category')
            ->where('name', 'like', "%$query%")
            ->take(5)
            ->get();

        $productsearch = DB::table('products')
            ->select(
                'products.*',
                DB::raw('(SELECT image50 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image50')
            )
            ->where('name', 'like', "%$query%")
            ->take(5)
            ->get();

        $stores = DB::table('shop')
            ->where('name', 'like', "%$query%")
            ->take(5)
            ->get();

        return compact('categories', 'productsearch', 'stores');
    }

    function fullSearch($query, $where = null, $category = null)
    {
        $keyword = $query;

        if ($category != null) {
            // Ambil kategori yang dipilih
            $selectedCategory = DB::table('product_category')
                ->where('id', $category)
                ->first();

            if ($selectedCategory) {
                $menuCategories = [
                    [
                        'id' => $selectedCategory->id,
                        'name' => $selectedCategory->name,
                        'submenus' => []
                    ]
                ];

                // Ambil submenu dari kategori yang dipilih
                $subMenus = DB::table('product_category')
                    ->where('parent_id', $category)
                    ->where('active', 'Y')
                    ->get();

                foreach ($subMenus as $subMenu) {
                    $menuCategories[0]['submenus'][] = [
                        'id' => $subMenu->id,
                        'name' => $subMenu->name
                    ];
                }
            } else {
                $menuCategories = [];
            }
        } else {
            // Kode yang sudah ada untuk menampilkan semua kategori
            $categories = DB::table('product_category')
                ->where('parent_id', 0)
                ->orWhere('level', 1)
                ->where('active', 'Y')
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
            ->leftJoin('shop as s', 'products.id_shop', '=', 's.id')
            ->leftJoin('member_address as ma', 's.id_address', '=', 'ma.member_address_id')
            ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id')
            ->where('products.name', 'like', "%$query%")
            ->where('products.status_display', 'Y')
            ->where('products.status_delete', 'N')
            ->where('s.status', 'active');

        if ($where) {
            foreach ($where as $key => $value) {
                $productsearch->where($key, $value);
            }
        }
        $productsearch->get();

        $stores = DB::table('shop')
            ->where('name', 'like', "%$query%")
            ->limit(3)
            ->get();

        return compact('menuCategories', 'productsearch', 'stores', 'keyword');
    }

    public function filterSearching($data)
    {
        $query = DB::table('products')
            ->select(
                'products.name',
                'shop.name as shop_name',
                'p.province_name',
                'lp.price_lpse as hargaTayang',
                DB::raw('(SELECT image300 FROM product_image WHERE id_product = products.id AND is_default = "yes" LIMIT 1) AS image')
            )
            ->leftJoin('shop', 'products.id_shop', '=', 'shop.id')
            ->leftJoin('lpse_price as lp', 'products.id', '=', 'lp.id_product')
            ->leftJoin('member_address as ma', 'shop.id_address', '=', 'ma.member_address_id')
            ->leftJoin('province as p', 'ma.province_id', '=', 'p.province_id');

        if (!empty($data['keyword'])) {
            $query->where('products.name', 'like', '%' . $data['keyword'] . '%');
        }

        if (!empty($data['category'])) {
            $query->leftJoin('product_category as pc', 'products.id_category', '=', 'pc.id');
            $query->where('products.id_category', $data['category']);
            $query->orWhere('pc.parent_id', $data['category']);
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

        if (!empty($data['sort'])) {
            if ($data['sort'] == 'terbaru') {
                $query->orderBy('products.created_at', 'desc');
            } elseif ($data['sort'] == 'h_tertinggi') {
                $query->orderBy('lp.price_lpse', 'desc');
            } elseif ($data['sort'] == 'terjual') {
                $query->orderBy('products.count_sold', 'desc');
            } elseif ($data['sort'] == 'h_terendah') {
                $query->orderBy('lp.price_lpse', 'asc');
            }
        }

        $productsearch = $query->get();

        return $productsearch;
    }

    function SaveLogSearch($keyword, $id_product = null, $id_user = null, $count_result = null) {
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
