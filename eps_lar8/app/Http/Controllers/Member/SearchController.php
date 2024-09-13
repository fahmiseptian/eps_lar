<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Searchengine;
use App\Models\Member;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Shop;
use Illuminate\Http\Request;

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
        $result = $this->libraries['Searchengine']->fullSearch($query);

        if ($result && isset($result['productsearch']) && $result['productsearch']->count() > 0) {
            $this->libraries['Searchengine']->SaveLogSearch(
                $query,
                $result['productsearch']->get()[0]->id,
                $this->data['id_user'],
                $result['productsearch']->count()
            );
        }

        return view('member.home.search', $this->data, $result);
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
        ];
        $result = $this->libraries['Searchengine']->filterSearching($data);
        return response()->json($result);
    }
}
