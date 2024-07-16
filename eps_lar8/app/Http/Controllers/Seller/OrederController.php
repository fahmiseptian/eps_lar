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
use App\Models\Lpse_config;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use Dompdf\Adapter\PDFLib;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\Json;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf as ImageGeneratorsPdf;

class OrederController extends Controller
{
    protected $seller;
    protected $getOrder;
    protected $data;
    protected $Model;
    protected $Liberies;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $this->getOrder =DB::table('complete_cart_shop as ccs')
        ->select(
            'ccs.id',
            'cc.invoice',
            'cc.status_pembayaran_top',
            'cc.created_date',
            'm.instansi as member_instansi',
            'c.city_name as city',
            'ccs.total',
            'ccs.qty',
            'ccs.status',
        )
        ->join('complete_cart as cc', 'ccs.id_cart', '=', 'cc.id')
        ->join('complete_cart_address as cca', 'ccs.id_cart', '=', 'cca.id_cart')
        ->join('member as m', 'cc.id_user', '=', 'm.id')
        ->join('member_address as ma', 'm.id', '=', 'ma.member_id')
        ->join('city as c', 'cca.city_id', '=', 'c.city_id');

        $this->seller 	= $request->session()->get('seller_id');
        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);

        // Membuat $this->data
        $this->data['title'] = 'order';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        $this->Model['CompleteCartShop']= new CompleteCartShop();

        $this->Liberies['terbilang']    = new Terbilang();

    }

    public function index()
    {
        $id_seller=$this->seller;
        $data = [
        ];

        $orders = CompleteCartShop::filterorder($id_seller,$data);

        return view('seller.order.index',$this->data,['orders' => $orders]);
        // return response()->json(['orders' => $orders]);
    }

    public function filterOrder($status_order)
    {
        $status = $status_order;
        $id_seller = $this->seller;

        if ($status == 'done') {
            $data = ['ccs.status' => 'complete'];
            $filterorders = CompleteCartShop::filterorder($id_seller, $data);
        } elseif ($status == 'complete') {
            $data = [
                'ccs.status' => 'complete',
                'cc.status_pembayaran_top' => 0,
            ];
            $filterorders = CompleteCartShop::filterorder($id_seller, $data);
        } else {
            $data = ['ccs.status' => $status];
            $filterorders = CompleteCartShop::filterorder($id_seller, $data);
        }

        if (request()->ajax()) {
            return response()->json($filterorders);
        }

        return view('seller.order.index', $this->data, ['orders' => $filterorders]);
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


    public function uploadDo(Request $request) {
        // Validasi data
        $id_cart_shop = $request->input('id_cart_shop');
        $id_shop = $this->seller; // Pastikan $this->seller sudah diinisialisasi sebelumnya
        $fileDo = $request->file('file_Do'); // Ambil file dari permintaan

        // Mencari CompleteCartShop berdasarkan ID dan ID toko
        $cartShop = CompleteCartShop::where('id', $id_cart_shop)
                                    ->where('id_shop', $id_shop)
                                    ->first();

        if (!$cartShop) {
            // Jika tidak ditemukan, kirim respon error
            return response()->json(['status' => 'error', 'message' => 'CartShop not found'], 404);
        }

        // Tambahkan file ke koleksi media dengan menyimpan ekstensi file asli
        $cartShop->addMedia($fileDo)
                ->usingFileName(time() . '.' . $fileDo->getClientOriginalExtension()) // Gunakan waktu ditambah ekstensi file asli
                ->toMediaCollection('file_DO', 'file_DO'); // Koleksi dan disk yang ditentukan

        // Perbarui atribut `file_do`, `status`, dan `delivery_end` pada objek CompleteCartShop
        $cartShop->file_do = 1;
        $cartShop->status = 'complete';
        $cartShop->delivery_end = now(); // Set waktu sekarang sebagai `delivery_end`
        $cartShop->save(); // Simpan perubahan pada database

        // Kembalikan respon JSON sukses
        return response()->json(['status' => 'success']);
    }


    public function lacak_kurir_sendiri($id_cart_shop) {
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

        $dataPembeli    = $this->Model['CompleteCartShop']->getUserById_cart_shop($id_cart_shop);
        $dataSeller     = $this->Model['CompleteCartShop']->getSellerById_cart_shop($id_cart_shop);

        $pdf = FacadePdf::loadView('pdf.newInvoice', ['data' => $detail_order, 'dataPembeli'=>$dataPembeli, 'dataSeller'=>$dataSeller]);

        return $pdf->stream('informasi_invoice.pdf');
    }

    public function generateKwantasiPDF($id_cart_shop)
    {
        $terbilang = $this->Liberies['terbilang'];
        $detail_order  = CompleteCartShop::getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->seller_address=Shop::getAddressByIdshop($this->seller);
        $detail_order->terbilang = $terbilang->terbilang($detail_order->total);
        $detail_order->tgl_indo = $terbilang->tgl_indo(date('Y-m-d', strtotime($detail_order->created_date)));
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        $dataPembeli    = $this->Model['CompleteCartShop']->getUserById_cart_shop($id_cart_shop);

        $pdf = FacadePdf::loadView('pdf.Kwitansi', ['data' => $detail_order,'eps'=>$eps, 'dataPembeli'=>$dataPembeli]);

        return $pdf->stream('Kwitansi.pdf');
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

    function getKontrak(Request $request) {
        $id_cart_shop = $request->idcs;
        $kontrak    = DB::table('kontrak')->where('id_complete_cart_shop',$id_cart_shop)->first();

        if (!$kontrak) {
            return response()->json(0);
        }
        return response()->json($kontrak);
    }

    function getorder($id_cart_shop){
        $order  = CompleteCartShop::getDetailOrderbyId($this->seller, $id_cart_shop);

        $order->terbilang = $this->Liberies['terbilang']->terbilang($order->total);
        $order->tgl_indo = $this->Liberies['terbilang']->tgl_indo(date('Y-m-d', strtotime("+3 day", strtotime($order->created_date))));

        $order->detail=CompleteCartShop::getDetailProduct($this->seller,$id_cart_shop);
        $htmlContent = view('pdf.kontrak', ['order' => $order])->render();

        // Mengirim respons JSON dengan data order dan konten HTML
        return response()->json([
            'order' => $order,
            'htmlContent' => $htmlContent,
        ]);
    }


    public function generateKontrak(Request $request)
    {
        // Ambil data dari form
        $id_cart_shop       = $request->id_cs;
        $no_kontrak         = $request->no_kontrak;
        $total_harga        = $request->total_harga;
        $tanggal_kontrak    = $request->tanggal_kontrak;
        $nilai_kontrak      = $request->nilai_kontrak;
        $catatan            = $request->catatan;
        $content            = $request->content;

        $dataKontrak = CompleteCartShop::GetIdSellerAndId_memeber($id_cart_shop);

        $dataArr = [
            'id_complete_cart_shop' => $id_cart_shop,
            'no_kontrak'=>$no_kontrak,
            'id_shop' => $dataKontrak->id_shop,
            'member_id' => $dataKontrak->id_user,
            'total_harga' => $total_harga,
            'nilai_kontrak' => $nilai_kontrak,
            'tanggal_kontrak' => $tanggal_kontrak,
            'catatan' => $catatan,
            'document' => $content,
            'update_date' => Carbon::now(),
        ];

        $check = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->count();

        if ($check > 0) {
            // Ambil nilai is_seller_input sebelumnya
            $existingData = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->first();
            $currentIsSellerInput = $existingData->is_seller_input ?? 0;

            $data = array_merge([
                'created_date'=>Carbon::now(),
                'is_seller_input'=> $currentIsSellerInput + 1
            ],$dataArr);

            $update = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->update($data);
        } else {
            $data = array_merge([
                'created_date'=>Carbon::now(),
                'is_seller_input'=>1
            ],$dataArr);

            $insert = DB::table('kontrak')->insert($data);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    function downloadKontrak(Request $request) {
        $id_cart_shop = $request->idcs;
        $kontrak = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->first();

        if (!$kontrak) {
            return response()->json(['error' => 'Kontrak tidak ditemukan'], 404);
        }

        $content = $kontrak->document;

        // Setup Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Load HTML content
        $dompdf->loadHtml($content);

        // Render PDF (optional settings)
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF to string
        $pdfOutput = $dompdf->output();

        // Membuat respons untuk mengunduh file PDF
        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="kontrak_' . time() . '.pdf"',
            'Cache-Control' => 'public, must-revalidate, max-age=0',
            'Pragma' => 'public',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT'
        ]);
    }

}

