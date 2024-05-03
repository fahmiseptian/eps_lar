<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Libraries\Terbilang;
use App\Models\Bast;
use App\Models\BastDetail;
use App\Models\Shop;
use App\Models\Saldo;
use App\Models\CompleteCartShop;
use App\Models\CompleteCartAddress;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Dompdf\Adapter\PDFLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Json;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf as ImageGeneratorsPdf;

class OrederController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $this->getOrder =DB::table('complete_cart_shop as ccs')
                    ->select('ccs.id', 'cc.invoice', 'cc.status_pembayaran_top','cc.created_date', 'm.instansi as member_instansi', 'c.city_name as city', 'ccs.total', 'ccs.qty','ccs.status')
                    ->join('complete_cart as cc', 'ccs.id_cart', '=', 'cc.id')
                    ->join('member as m', 'cc.id_user', '=', 'm.id')
                    ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
                    ->join('city as c', 'ma.city_id', '=', 'c.city_id');

                    $this->seller 	= $request->session()->get('seller_id');

                    $sellerType     = Shop::getTypeById($this->seller);
                    $saldoPending   = Saldo::calculatePendingSaldo($this->seller);
                    
                    // Membuat $this->data
                    $this->data['title'] = 'order';
                    $this->data['seller_type'] = $sellerType;
                    $this->data['saldo'] = $saldoPending;
    }

    public function index()
    {
        $id_seller=$this->seller;
        $orders= $this->getOrder
                ->where('ccs.id_shop', '=', $id_seller)
                ->orderBy('cc.invoice', 'desc')
                ->get();

        return view('seller.order.index',$this->data,['orders' => $orders]);
    }

    public function filterOrder($status_order){
        $status = $status_order;
        $shopId = $this->seller;

        if ($status=='done') {
            $filterorders = $this->getOrder
                ->where('ccs.id_shop', '=', $shopId)
                ->where('ccs.status','=','complete')
                ->where('cc.status_pembayaran_top','=',1)
                ->orderBy('cc.invoice', 'desc')
                ->get();
        }elseif($status=='complete'){
            $filterorders = $this->getOrder
                ->where('ccs.id_shop', '=', $shopId)
                ->where('ccs.status','=','complete')
                ->where('cc.status_pembayaran_top','=',0)
                ->orderBy('cc.invoice', 'desc')
                ->get();
        }else {
            $filterorders = $this->getOrder
                ->where('ccs.id_shop', '=', $shopId)
                ->where('ccs.status','=',$status)
                ->orderBy('cc.invoice', 'desc')
                ->get();
        }
        return view('seller.order.index',$this->data,['orders' => $filterorders]);
    }

    public function detailOrder($id_cart_shop){
        $shopId = $this->seller;
        $detailOrder= CompleteCartShop::getDetailOrderbyId($shopId,$id_cart_shop);
        $detailProductorder=CompleteCartShop::getDetailProduct($shopId,$id_cart_shop);

        return view('seller.order.detail',$this->data,['detailOrder'=>$detailOrder, 'detailProductOrder'=>$detailProductorder]);
    }

    public function lacakResi(Request $request){
        $shopId = $this->seller;
        $id_cart_shop = $request->input('id_cart_shop');
        $detailOrder= CompleteCartShop::getDetailOrderbyId($shopId,$id_cart_shop);

        return response()->json(['detailOrder'=> $detailOrder]);
    }

    public function acceptOrder(Request $request) {
        $id_cart_shop = $request->input('id_cart_shop');
        $estimation_packing=Shop::where('id', $this->seller)
                        ->pluck('packing_estimation')
                        ->first();
        $order = CompleteCartShop::find($id_cart_shop);
        $current_date= date('Y-m-d H:i:s');
        $due_date_packing = date('Y-m-d H:i:s', strtotime($current_date . ' +' . $estimation_packing . ' day'));
        
        if ($order && $estimation_packing ) {
            $order->status = 'on_packing_process';
            $order->receive_date = $current_date;
            $order->due_date_packing = $due_date_packing; 
            $order->save();
            return "Pesanan berhasil diterima dan sedang diproses packing.";
        } else {
            return "Pesanan tidak ditemukan.";
        }
    }

    public function cancelOrder(Request $request) {
        $id_cart_shop = $request->input('id_cart_shop');
        $note = $request->input('note');

        $order = CompleteCartShop::find($id_cart_shop)->where('id_shop', $this->seller);
        $current_date= date('Y-m-d H:i:s');
        
        if ($order) {
            $order->status = 'cancel_by_seller';
            $order->note_seller =  $note;
            $order->receive_date = $current_date;
            $order->save();
            return "Pesanan berhasil diterima dan sedang diproses packing.";
        } else {
            return "Pesanan tidak ditemukan.";
        }
    }
    
    public function updateResi(Request $request)
    {
        // Validasi data
        $id_cart_shop = $request->input('id');
        $nomor_resi = $request->input('nomor_resi');
        $id_shop = $this->seller;

        // Mencari CartShop berdasarkan id
        $cartShop = CompleteCartShop::where('id', $id_cart_shop)
                            ->where('id_shop', $id_shop)
                            ->first();

        if ($cartShop) {
            // Memperbarui nomor resi
            $cartShop->no_resi = $nomor_resi;
            $cartShop->status = 'send_by_seller';
            $cartShop->delivery_start = now();
            $cartShop->save();

            return response()->json(['status' => 'success', 'message'=> 'Nomor resi berhasil diperbarui']);
        } else {
            // Mengembalikan respons error jika id tidak ditemukan
            return response()->json(['status' => 'error', 'message'=> 'ID tidak ditemukan']);
        }
    }

    public function uploadDo(Request $request){
        // Validasi data
        $id_cart_shop = $request->input('id_cart_shop');
        $id_shop = $this->seller;
        $file_Do= $request->file('file_Do');

        // Mencari CartShop berdasarkan id
        $cartShop = CompleteCartShop::where('id', $id_cart_shop)
                            ->where('id_shop', $id_shop)
                            ->first();

        $cartShop->addMedia($request->file('file_Do'))
                ->usingFileName(time())
                ->toMediaCollection('file_DO', 'file_DO');

        $cartShop->file_do = 1;
        $cartShop->status = 'complete';
        $cartShop->delivery_end = now();
        $cartShop->save();                

        return response()->json(['status' => 'success']);
    }

    public function test($id_cart_shop) {
        $ccs = CompleteCartShop::getorderbyidcartshop($this->seller,$id_cart_shop);
        
        if ($ccs) {
            return response()->json(['ccs' => $ccs]);
        } else {
            return response()->json(['error' => 'CompleteCartShop not found'], 404);
        }
    }

    public function generateResiPDF($id_cart_shop)
    {
        $detail_order  = CompleteCartShop::getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->detail=CompleteCartShop::getDetailProduct($this->seller,$id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($this->seller);


        $pdf = FacadePdf::loadView('pdf.resi', ['data' => $detail_order]);
        
        return $pdf->stream('informasi_pengiriman.pdf');
    }

    public function generateINVPDF($id_cart_shop)
    {
        $detail_order  = CompleteCartShop::getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->detail=CompleteCartShop::getDetailProduct($this->seller,$id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($this->seller);
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];
    

        $pdf = FacadePdf::loadView('pdf.invoice', ['data' => $detail_order,'eps'=>$eps]);
        
        return $pdf->stream('informasi_invoice.pdf');
    }

    public function generateKwantasiPDF($id_cart_shop)
    {
        $terbilang = new Terbilang();
        $detail_order  = CompleteCartShop::getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($this->seller);
        $detail_order->terbilang = $terbilang->terbilang($detail_order->total);
        $detail_order->tgl_indo = $terbilang->tgl_indo(date('Y-m-d', strtotime($detail_order->created_date)));
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];
    

        $pdf = FacadePdf::loadView('pdf.kwantasi', ['data' => $detail_order,'eps'=>$eps]);
        
        return $pdf->stream('informasi_kwantasi.pdf');
    }

    public function generateBastPDF($id_cart_shop)
    {
        $detail_order  = CompleteCartShop::getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($this->seller);
        $bast = Bast::getBast($id_cart_shop);
        $data = BastDetail::getBAstbyIdBast($bast->id);
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        $pdf = FacadePdf::loadView('pdf.bast', ['data' => $detail_order,'eps'=>$eps,'bast'=>$bast, 'data_qty'=>$data]);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => TRUE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );
        return $pdf->stream('informasi_bast.pdf');
    }
}

