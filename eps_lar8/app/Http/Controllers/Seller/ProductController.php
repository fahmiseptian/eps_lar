<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $sellerType =Shop::where('id', $this->seller)
                    ->pluck('type')
                    ->first();

        $saldo =Saldo::where('id_shop', $this->seller)
                ->where('status', 'pending')
                ->sum('total_diterima_seller');

        $this->data['title'] = 'Product';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldo;
    }

    public function index()
    {
        $this->data['tipe'] = null;
        $products =DB::table('product as p')
                ->select(
                    'p.*',
                    'lp.price_lpse as price_tayang'
                    )
                ->join('lpse_price as lp','lp.id_product','=','p.id')
                ->where('p.id_shop',$this->seller)
                ->where('p.status_delete','N')
                ->orderBy('id', 'desc')
                ->get();
        return view('seller.product.index',$this->data,['products'=>$products]);
    }

    public function filterProduct($status) {
        $this->data['tipe'] = $status;
        $products = DB::table('product as p')
                    ->select(
                        'p.*',
                        'lp.price_lpse as price_tayang'
                    )
                    ->join('lpse_price as lp', 'lp.id_product', '=', 'p.id')
                    ->where('p.id_shop', $this->seller)
                    ->where('p.status_delete', 'N');
    
        if ($status == 'live') {
            $products->where('p.status_display', 'Y');
        } elseif ($status == 'habis') {
            $products->where('p.stock', 0);
        } elseif ($status == 'arsip') {
            $products->where('p.status_display', 'N');
        }
    
        $products = $products->orderBy('id', 'desc')->get();
        
        return view('seller.product.index', $this->data, ['products' => $products]);
    }
    
    
}

