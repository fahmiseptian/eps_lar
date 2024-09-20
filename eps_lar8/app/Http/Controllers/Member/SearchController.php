<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Searchengine;
use App\Models\Member;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{

    protected $data;
    protected $model;
    protected $libraries;
    public function __construct(Request $request)
    {
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
        $this->model['member'] = new Member();

        $this->libraries['Searchengine'] = new Searchengine();

        if ($this->data['id_user'] != null) {
            $this->data['member'] = $this->model['member']->find($this->data['id_user']);
            $this->data['nama_user'] = $this->data['member']->nama;
        }
    }

    public function quickSearch(Request $request)
    {
        $query = $request->input('query');

        $result = $this->libraries['Searchengine']->quickSearch($query);
        return view('member.asset.quick_search_results', $result);
    }

    public function fullSearch($query)
    {
        $result = $this->libraries['Searchengine']->fullSearch($query, null, null, 20, 1);

        if ($result && isset($result['productsearch']) && $result['productsearch']->total() > 0) {
            $firstProduct = $result['productsearch']->first();
            if ($firstProduct) {
                $this->libraries['Searchengine']->SaveLogSearch(
                    $query,
                    $firstProduct->id,
                    $this->data['id_user'],
                    $result['productsearch']->total()
                );
            }
        }
        Log::info('Search called with query: ' . $query);

        // Jangan panggil get() di sini
        // $result['productsearch'] = $result['productsearch']->get(); // Hapus atau komentari baris seperti ini jika ada

        return view('member.home.search', $this->data, $result);
        // return response()->json($result);
    }


    public function filterSearching(Request $request)
    {
        $data = [
            'category' => $request->input('category'),
            'keyword' => $request->input('keyword'),
            'max' => $request->input('max'),
            'min' => $request->input('min'),
            'condition' => $request->input('condition'),
            'sort' => $request->input('sort'),
            'perPage' => $request->input('perPage'),
            'page' => $request->input('page'),
        ];
        $result = $this->libraries['Searchengine']->filterSearching($data);
        return response()->json($result);
    }

    function more_product(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page');

        // return response()->json(["query" => $query, "page" => $page]);

        $result = $this->libraries['Searchengine']->fullSearch($query, null, null, 20, $page);

        if ($request->ajax()) {
            return view('member.home.product_searching', $result)->render();
        }

        return view('member.home.search', $this->data, $result);
    }

    function SerachwithCategory($category)
    {
        $result = $this->libraries['Searchengine']->fullSearch(null, null, $category, 20, 1);
        // return response()->json($result);
        return view('member.home.search', $this->data, $result);
    }

    function filterProductwithIdshop(Request $request)
    {
        $idshop = $request->input('idshop');
        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $sort = $request->input('condition');
        if ($category == 0) {
            $category = null;
        }

        $data = [
            'idshop' => $idshop,
            'keyword' => $keyword,
            'category' => $category,
            'sort' => $sort
        ];
        $result = $this->libraries['Searchengine']->filterSearching($data);
        return response()->json($result);
    }
}
