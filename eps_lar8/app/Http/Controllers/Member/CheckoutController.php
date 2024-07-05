<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libraries\Terbilang;
use App\Models\Bast;
use App\Models\Cart;
use App\Models\CartShopTemporary;
use App\Models\CompleteCartAddress;
use App\Models\CompleteCartShop;
use App\Models\CompleteCartShopDetail;
use App\Models\Invoice;
use App\Models\Shop;
use App\Models\UploadPayment;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class CheckoutController extends Controller
{
    protected $data;
    public function __construct(Request $request)
    {
        $this->data['complate_cart'] = new Invoice();
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
    }

    function getOrder($id_cart) {
        // Ke table complate_cart
        $orders = $this->data['complate_cart']->getOrder($id_cart);
        $address_buyyer = CompleteCartAddress::getaddressOrderBuyyer($id_cart);
        $ordershop = CompleteCartShop::getorderbyIdCart($id_cart);

        $orders->buyyer = $address_buyyer;
        $orders->detail = $ordershop;

        $total_barang_dengan_PPN = 0;
        $total_barang_tanpa_PPN = 0;
        $total_shipping = 0;
        $total_insurance = 0;
        $total_ppn_product = 0;
        $total_ppn_shipping = 0;
        $total_diskon = 0;
        $total_handling_cost_non_ppn = 0;

        if ($orders->status_pembayaran_top == '1' ) {
            $orders->status = 'Lunas';
        }elseif ($orders->status_pembayaran_top == '0' && $orders->file_upload != null) {
            $orders->status = 'Menunggu Pengecekan Pembayaran';
        }else{
            $orders->status = 'Belum Bayar';
        }

        foreach ($orders->detail as $detail) {
            $products = CompleteCartShopDetail::getProductOrder($detail->id_shop,$id_cart);

            $detail->products = $products['carts'];
            $detail->total_ppn = $detail->ppn_price + $detail->ppn_shipping;
            $detail->total_shipping = $detail->sum_shipping + $detail->ppn_shipping + $detail->insurance_nominal;
            $detail->total_barang_dengan_PPN = $products['total_barang_dengan_PPN'];
            $detail->total_barang_tanpa_PPN = $products['total_barang_tanpa_PPN'];


            $total_barang_dengan_PPN += $detail->total_barang_dengan_PPN;
            $total_barang_tanpa_PPN += $detail->total_barang_tanpa_PPN;
            $total_shipping += $detail->sum_shipping;
            $total_insurance += $detail->insurance_nominal;
            $total_ppn_product += $detail->ppn_price;
            $total_ppn_shipping += $detail->ppn_shipping;
            $total_diskon += $detail->discount;
            $total_handling_cost_non_ppn += $detail->handling_cost_non_ppn;
        }

        $orders->total_handling_cost_non_ppn = $total_handling_cost_non_ppn;
        $orders->total_diskon = $total_diskon;
        $orders->total_barang_dengan_PPN = $total_barang_dengan_PPN;
        $orders->total_barang_tanpa_PPN = $total_barang_tanpa_PPN;
        $orders->total_shipping = $total_shipping;
        $orders->total_insurance = $total_insurance;
        $orders->total_ppn = $total_ppn_product + $total_ppn_shipping;

        return response()->json(['order' => $orders]);
    }

    function uploadPayment(Request $request){
        $id_cart = $request->id_cart;
        $img = $request->file('img');

        $order = $this->data['complate_cart']->getOrder($id_cart);
        $date = now();

        $payment = UploadPayment::create([
            'invoice' => $order->invoice,
            'nominal' => $order->total,
            'created_dt' => $date,
            'file_upload' => ''
        ]);

        if ($img) {
            $payment->addMedia($img)
                    ->usingFileName(time() . '.' . $img->getClientOriginalExtension())
                    ->toMediaCollection('upload_payment', 'upload_payment');
        }

        $payment->update(['file_upload' => $payment->url_img_payment]);

        return response()->json(['payment' => $payment]);
    }

    function sumbit_bast(Request $request){
        $id_cart = $request->id_cart;
        $id_cart_shop = $request->id_cs;

        $data_bast = [
            'id_cart' => $id_cart,
            'id_cart_shop' => $id_cart_shop,
            'upload_image' => ''
        ];

        $CompleteCartShop= new CompleteCartShop;

        $detail = CompleteCartShop::select('id_shop','qty')->where('id',$id_cart_shop)->first();
        $id_bast 	= Bast::insertBast($data_bast);

        $products = CompleteCartShopDetail::getProductOrder($detail->id_shop,$id_cart);
        $detail->products = $products['carts'];

        $data_qty_diterima = json_decode($request->qty_diterima);
        $data_qty_dikembalikan = json_decode($request->qty_dikembalikan);

        foreach ($data_qty_diterima as $index => $item) {
            $qty_dikembalikan = $data_qty_dikembalikan[$index];

            $data_bast_detail[] = [
                'id_bast' 			=> $id_bast,
                'id_product'        => $item->id,
                'qty' 				=> $detail->qty,
                'qty_diterima'      => $item->qty_diterima,
                'qty_dikembalikan'  => $qty_dikembalikan->qty_dikembalikan,
            ];
        }

		$bast_detail 	= Bast::insertBastDetail($data_bast_detail);
        $receive_order 	= $CompleteCartShop->receiveOrder($id_cart, $id_cart_shop);

        // return $data_bast_detail ;
        // $report = $this->receive_order($data_bast['id_cart'], $data_bast['id_cart_shop']);
    }

    public function cetak_Invoice($id_shop,$id_cart_shop) {
        $detail_order  = CompleteCartShop::getDetailOrderbyId($id_shop, $id_cart_shop);
        $detail_order->detail=CompleteCartShop::getDetailProduct($id_shop, $id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($id_shop);
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        // return response()->json(['detail_order' => $detail_order]);
        $pdf = FacadePdf::loadView('pdf.invoice', ['data' => $detail_order,'eps'=>$eps]);

        return $pdf->stream('informasi_invoice.pdf');
    }

    public function cetak_Kwitansi($id_shop,$id_cart_shop) {
        $terbilang = new Terbilang();
        $detail_order  = CompleteCartShop::getDetailOrderbyId($id_shop, $id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($id_shop);
        $detail_order->terbilang = $terbilang->terbilang($detail_order->total);
        $detail_order->tgl_indo = $terbilang->tgl_indo(date('Y-m-d', strtotime($detail_order->created_date)));
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        // return response()->json(['detail_order' => $detail_order]);
        $pdf = FacadePdf::loadView('pdf.kwantasi', ['data' => $detail_order,'eps'=>$eps]);
        return $pdf->stream('informasi_kwantasi.pdf');
    }

    public function lacak_pengiriman($id_shop, $id_cart_shop) {
        $ccs = CompleteCartShop::getorderbyidcartshop($id_shop,$id_cart_shop);

        if ($ccs) {
            return response()->json(['ccs' => $ccs]);
        } else {
            return response()->json(['error' => 'CompleteCartShop not found'], 404);
        }
    }

    function get_detail_product($id_shop,$id_cart_shop) {
        $cart_shop = CompleteCartShop::getDetailOrderbyId($id_shop, $id_cart_shop);
        $products = CompleteCartShopDetail::getProductOrder($cart_shop->id_shop,$cart_shop->id_cart);

        $cart_shop->products = $products['carts'] ;
        return response()->json(['cart_shop' => $cart_shop]);
    }

    function testBNIVA(){
        $va = Cart::VA_BNI();

        return $va;
    }
}
