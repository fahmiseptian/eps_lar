<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\CartShopTemporary;
use App\Models\CompleteCartAddress;
use App\Models\CompleteCartShop;
use App\Models\CompleteCartShopDetail;
use App\Models\Invoice;
use App\Models\UploadPayment;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $orders->status = 'Menunggu Persetujuan';
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


}
