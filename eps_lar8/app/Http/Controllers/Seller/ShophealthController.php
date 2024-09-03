<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\PenarikanDana;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Libraries\VerificationService;
use App\Models\Chatdetail;
use App\Models\CompleteCartShop;
use App\Models\Member;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\Violation;

class ShophealthController extends Controller
{
    protected $user_id;
    protected $username;
    protected $seller;
    protected $data;
    protected $menu;
    protected $Model;
    protected $verificationService;

    public function __construct(Request $request)
    {
        $this->seller     = $request->session()->get('seller_id');

        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);
        $this->data['seller_type']     = Shop::getTypeById($this->seller);
        $this->data['saldo'] = $saldoPending;

        $this->Model['Violation'] = new Violation();
        $this->Model['Products'] = new Products();
        $this->Model['ProductCategory'] = new ProductCategory();
        $this->Model['CompleteCartShop'] = new CompleteCartShop();
        $this->Model['Chatdetail'] = new Chatdetail();
        $this->verificationService = new VerificationService;
    }

    public function index()
    {
        return view('seller.health.index');
    }

    public function info_toko()
    {
        return view('seller.health.informasi');
    }

    function get_health()
    {

        $this->data['pelanggaran_produk_berat'] = $this->Model['Violation']->countViolation($this->seller, "berat");
        $this->data['produk_spam'] = $this->Model['Violation']->countViolation($this->seller, null, ['pv.id' => '7']);
        $this->data['produk_imitasi'] = $this->Model['Violation']->countViolation($this->seller, null, ['pv.id' => '15']);
        $this->data['produk_yang_dilarang'] = $this->Model['Violation']->countViolation($this->seller, null, ['pv.id' => '4']);
        $this->data['pelanggaran_produk_ringan'] = $this->Model['Violation']->countViolation($this->seller, "ringan");
        $this->data['count_order'] = $this->Model['CompleteCartShop']->countAllorder($this->seller);
        $this->data['tidak_terselesaikan'] = $this->Model['CompleteCartShop']->get_count_order($this->seller, 'cancel_by_time');
        $this->data['pembatalan'] = $this->Model['CompleteCartShop']->get_count_order($this->seller, 'cancel_by_seller');
        $this->data['pengembalian'] = $this->Model['CompleteCartShop']->get_count_order($this->seller, 'refund');
        $this->data['pengemasan'] = $this->Model['CompleteCartShop']->get_count_order($this->seller, 'on_packing_process');
        $this->data['total_chat_time'] =  $this->Model['Chatdetail']->getChatSummary($this->seller);
        $this->data['total_percentage_chat'] =  $this->Model['Chatdetail']->get_percentage_chat($this->seller);
        return response()->json($this->data, 200);
    }

    function faq() {
        return response()->json(200);
    }

    function get_informasi_toko()
    {
        $ccs = $this->Model['CompleteCartShop']->getSummaryCompleteCartShop($where=array('id_shop' => $this->seller ));
        $log_view =$this->Model['CompleteCartShop']->getCountViewShop($this->seller);
        $produk_view =$this->Model['CompleteCartShop']->getCountViewProduct($this->seller);
        $data = [];

        $data = [
            'penjualan' => $ccs->grandtotal,
            'pesanan' => $ccs->count,
            'views' => $log_view,
            'produk' => $produk_view
        ];
        return response()->json($data);
    }

    function get_rank_produk($status)
    {
        if ($status == 'penjualan') {
            $data = $this->Model['Products']->get_rank_produk_bySelling($this->seller);
        } elseif ($status == 'dilihat') {
            $data = $this->Model['Products']->get_rank_produk_bySeen($this->seller);
        } else {
            return response()->json(404);
        }

        return response()->json($data);
    }

    function get_rank_category()
    {
        $data = $this->Model['ProductCategory']->get_rank_category_byIdshop($this->seller);

        return response()->json($data);
    }
}
