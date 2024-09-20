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
        $this->seller     = $request->session()->get('seller_id');

        $this->getOrder = DB::table('complete_cart_shop as ccs')
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

        $this->seller     = $request->session()->get('seller_id');
        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);

        // Membuat $this->data
        $this->data['title'] = 'order';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;

        $this->Model['CompleteCartShop'] = new CompleteCartShop();
        $this->Model['Shop'] = new Shop();
        $this->Model['BastDetail'] = new BastDetail();

        $this->Liberies['terbilang']    = new Terbilang();
    }

    public function index()
    {
        return view('seller.order.index', $this->data);
    }

    public function filterOrder($status_order)
    {
        $status = $status_order;
        $id_seller = $this->seller;

        if ($status == 'done') {
            $data = ['ccs.status' => 'complete'];
            $filterorders = $this->Model['CompleteCartShop']->filterorder($id_seller, $data);
        } elseif ($status == 'semua') {
            $data = [];
            $filterorders = $this->Model['CompleteCartShop']->filterorder($id_seller, $data);
        } elseif ($status == 'complete') {
            $data = [
                'ccs.status' => 'complete',
                'cc.status_pembayaran_top' => 0,
            ];
            $filterorders = $this->Model['CompleteCartShop']->filterorder($id_seller, $data);
        } else {
            $data = ['ccs.status' => $status];
            $filterorders = $this->Model['CompleteCartShop']->filterorder($id_seller, $data);
        }

        if (request()->ajax()) {
            return response()->json($filterorders);
        }

        return view('seller.order.index', $this->data, ['orders' => $filterorders]);
    }

    public function detailOrder($id_cart_shop)
    {
        $shopId = $this->seller;
        $detailOrder = $this->Model['CompleteCartShop']->getDetailOrderbyId($shopId, $id_cart_shop);
        $detailProductorder = $this->Model['CompleteCartShop']->getDetailProduct($shopId, $id_cart_shop);

        $data = [
            'detailOrder' => $detailOrder,
            'produk' => $detailProductorder
        ];

        return response()->json($data);
        // return view('seller.order.detail', $this->data, ['detailOrder' => $detailOrder, 'detailProductOrder' => $detailProductorder]);
    }

    public function lacakResi(Request $request)
    {
        $shopId = $this->seller;
        $id_cart_shop = $request->input('id_cart_shop');
        $detailOrder = $this->Model['CompleteCartShop']->getDetailOrderbyId($shopId, $id_cart_shop);

        return response()->json(['detailOrder' => $detailOrder]);
    }

    public function acceptOrder(Request $request)
    {
        $id_cart_shop = $request->input('id_cart_shop');
        $estimation_packing = Shop::where('id', $this->seller)
            ->pluck('packing_estimation')
            ->first();
        $order = CompleteCartShop::where('id', $id_cart_shop)
            ->where('id_shop', $this->seller)
            ->first();
        $current_date = date('Y-m-d H:i:s');
        $due_date_packing = date('Y-m-d H:i:s', strtotime($current_date . ' +' . $estimation_packing . ' day'));

        if ($order && $estimation_packing) {
            $order->status = 'on_packing_process';
            $order->receive_date = $current_date;
            $order->due_date_packing = $due_date_packing;
            $order->save();
            return "Pesanan berhasil diterima dan sedang diproses packing.";
        } else {
            return "Pesanan tidak ditemukan.";
        }
    }

    public function cancelOrder(Request $request)
    {
        $id_cart_shop = $request->input('id_cart_shop');
        $note = $request->input('note');

        $order = CompleteCartShop::where('id', $id_cart_shop)
            ->where('id_shop', $this->seller)
            ->first();

        if ($order) {
            DB::beginTransaction();
            try {
                // Ubah status CompleteCartShop
                $order->status = 'cancel_by_seller';
                $order->note_seller = $note;
                $order->save();

                // Cek apakah ini satu-satunya CompleteCartShop untuk CompleteCart ini
                $completeCart = Invoice::find($order->id_cart);
                $otherShops = CompleteCartShop::where('id_cart', $order->id_cart)
                    ->where('id', '!=', $id_cart_shop)
                    ->count();

                if ($otherShops == 0) {
                    // Jika tidak ada CompleteCartShop lain, ubah status CompleteCart
                    $completeCart->status = 'cancel';
                    $completeCart->save();
                }

                DB::commit();
                return "Pesanan berhasil dibatalkan.";
            } catch (\Exception $e) {
                DB::rollback();
                return "Terjadi kesalahan saat membatalkan pesanan: " . $e->getMessage();
            }
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

            return response()->json(['status' => 'success', 'message' => 'Nomor resi berhasil diperbarui']);
        } else {
            // Mengembalikan respons error jika id tidak ditemukan
            return response()->json(['status' => 'error', 'message' => 'ID tidak ditemukan']);
        }
    }


    public function uploadDo(Request $request)
    {
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
        $media = $cartShop->addMedia($fileDo)
            ->usingFileName(time() . '.' . $fileDo->getClientOriginalExtension()) // Gunakan waktu ditambah ekstensi file asli
            ->toMediaCollection('file_DO', 'file_DO'); // Koleksi dan disk yang ditentukan

        // Perbarui atribut `file_do`, `status`, dan `delivery_end` pada objek CompleteCartShop
        $cartShop->file_do = $media->getUrl();
        $cartShop->status = 'complete';
        $cartShop->delivery_end = now(); // Set waktu sekarang sebagai `delivery_end`
        $cartShop->save(); // Simpan perubahan pada database

        // Kembalikan respon JSON sukses
        return response()->json(['status' => 'success']);
    }


    public function lacak_kurir_sendiri($id_cart_shop)
    {
        $ccs = $this->Model['CompleteCartShop']->getorderbyidcartshop($this->seller, $id_cart_shop);

        if ($ccs) {
            return response()->json(['ccs' => $ccs]);
        } else {
            return response()->json(['error' => 'CompleteCartShop not found'], 404);
        }
    }

    public function generateResiPDF($id_cart_shop)
    {
        $detail_order  = $this->Model['CompleteCartShop']->getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->detail = $this->Model['CompleteCartShop']->getDetailProduct($this->seller, $id_cart_shop);
        $detail_order->seller_address = $this->Model['Shop']->getAddressByIdshop($this->seller);

        // return response()->json(['detail_order' => $detail_order]);
        $pdf = FacadePdf::loadView('pdf.resi', ['data' => $detail_order]);

        return $pdf->stream('informasi_pengiriman.pdf');
    }

    public function generateINVPDF($id_cart_shop)
    {
        $detail_order  = $this->Model['CompleteCartShop']->getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->detail = $this->Model['CompleteCartShop']->getDetailProduct($this->seller, $id_cart_shop);

        $dataPembeli    = $this->Model['CompleteCartShop']->getaddressUser($id_cart_shop);
        $dataSeller     = $this->Model['CompleteCartShop']->getSellerById_cart_shop($id_cart_shop);

        $pdf = FacadePdf::loadView('pdf.newInvoice', ['data' => $detail_order, 'dataPembeli' => $dataPembeli, 'dataSeller' => $dataSeller]);

        return $pdf->stream('informasi_invoice.pdf');
    }

    public function generateKwantasiPDF($id_cart_shop)
    {
        $terbilang = $this->Liberies['terbilang'];
        $detail_order  = $this->Model['CompleteCartShop']->getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->seller_address = $this->Model['Shop']->getAddressByIdshop($this->seller);
        $detail_order->terbilang = $terbilang->terbilang($detail_order->total);
        $detail_order->tgl_indo = $terbilang->tgl_indo(date('Y-m-d', strtotime($detail_order->created_date)));
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        $dataPembeli    = $this->Model['CompleteCartShop']->getUserById_cart_shop($id_cart_shop);

        $pdf = FacadePdf::loadView('pdf.Kwitansi', ['data' => $detail_order, 'eps' => $eps, 'dataPembeli' => $dataPembeli]);

        return $pdf->stream('Kwitansi.pdf');
    }

    public function generateBastPDF($id_cart_shop)
    {
        $detail_order  = $this->Model['CompleteCartShop']->getDetailOrderbyId($this->seller, $id_cart_shop);
        $detail_order->seller_address = $this->Model['Shop']->getAddressByIdshop($this->seller);
        $bast = Bast::getBast($id_cart_shop);
        $data = $this->Model['BastDetail']->getBAstbyIdBast($bast->id);
        $eps = [
            'nama' => 'PT. Elite Proxy Sistem',
            'npwp' => ' 73.035.456.0-022.000',
            'alamat' => 'Rukan Sudirman Park Apartement Jl Kh. Mas Mansyur KAV 35 A/15 Kelurahan Karet Tengsin Kec. Tanah Abang Jakarta Pusat DKI Jakarta'
        ];

        // return response()->json(['data' => $detail_order, 'eps' => $eps, 'bast' => $bast, 'data_qty' => $data]);

        $pdf = FacadePdf::loadView('pdf.bast', ['data' => $detail_order, 'eps' => $eps, 'bast' => $bast, 'data_qty' => $data]);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed' => TRUE,
                    'verify_peer' => TRUE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );
        return $pdf->stream('informasi_bast.pdf');
    }

    public function uploadFaktur(Request $request)
    {
        // Validasi input
        $request->validate([
            'faktur' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
            'id_order_shop' => 'required|integer',
        ]);

        $file = $request->file('faktur');
        $id_cart_shop = $request->input('id_order_shop');

        $cartShop = CompleteCartShop::where('id', $id_cart_shop)
            ->where('id_shop', $this->seller)
            ->first();

        $media = $cartShop->addMedia($file)
            ->usingFileName(time() . '.' . $file->getClientOriginalExtension())
            ->toMediaCollection('faktur', 'faktur');

        $cartShop->file_pajak = $media->getUrl();
        $cartShop->save();

        return response()->json(['status' => 'success', 'message' => 'Faktur Berhasil Di Upload']);
    }

    function getKontrak(Request $request)
    {
        $id_cart_shop = $request->idcs;
        $kontrak    = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->first();

        if (!$kontrak) {
            return response()->json(0);
        }
        return response()->json($kontrak);
    }

    function getSuratPesanan(Request $request)
    {
        $id_cart_shop = $request->idcs;
        $sp    = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->first();

        if (!$sp) {
            return response()->json(0);
        }
        return response()->json($sp);
    }

    function getorder($id_cart_shop)
    {
        $order  = $this->Model['CompleteCartShop']->getDetailOrderbyId($this->seller, $id_cart_shop);

        $order->terbilang = $this->Liberies['terbilang']->terbilang($order->total);
        $order->tgl_indo = $this->Liberies['terbilang']->tgl_indo(date('Y-m-d', strtotime("+3 day", strtotime($order->created_date))));

        $order->detail = $this->Model['CompleteCartShop']->getDetailProduct($this->seller, $id_cart_shop);
        $htmlContent = view('pdf.kontrak', ['order' => $order])->render();

        // Mengirim respons JSON dengan data order dan konten HTML
        return response()->json([
            'order' => $order,
            'htmlContent' => $htmlContent,
        ]);
    }

    function getSP($id_cart_shop)
    {
        $order  = $this->Model['CompleteCartShop']->getDetailOrderbyId($this->seller, $id_cart_shop);

        $order->terbilang = $this->Liberies['terbilang']->terbilang($order->total);
        $order->tgl_indo = $this->Liberies['terbilang']->tgl_indo(date('Y-m-d', strtotime("+3 day", strtotime($order->created_date))));

        $order->pengiriman = $order->delivery_start ? date('d-m-y', $order->delivery_start) : null;

        if ($order->status == 'waiting_accept_order') {
            $order->status = 'Menunggu Seller Menerima Pesanan';
        } elseif ($order->status == 'on_packing_process') {
            $order->status = 'Proses Pengemasan Paket';
        } elseif ($order->status == 'send_by_seller') {
            $order->status = 'Dalam Pengiriman';
        } elseif ($order->status == 'complete') {
            $order->status = 'Paker Segera Tiba';
        } elseif ($order->status == 'complete' && $order->delivery_end != null) {
            $order->status = 'Paket Sampai';
        } elseif ($order->status == 'waiting_approve_by_ppk') {
            $order->status = 'Menunggu Persetujuan PPK';
        } else {
            $order->status = 'Pesanan Dibatalkan';
        }

        $order->detail = $this->Model['CompleteCartShop']->getDetailProduct($this->seller, $id_cart_shop);
        $htmlContent = view('pdf.S_pesanan', ['order' => $order])->render();

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

        $dataKontrak = $this->Model['CompleteCartShop']->GetIdSellerAndId_memeber($id_cart_shop);

        $dataArr = [
            'id_complete_cart_shop' => $id_cart_shop,
            'no_kontrak' => $no_kontrak,
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
                'created_date' => Carbon::now(),
                'is_seller_input' => $currentIsSellerInput + 1
            ], $dataArr);

            $update = DB::table('kontrak')->where('id_complete_cart_shop', $id_cart_shop)->update($data);
        } else {
            $data = array_merge([
                'created_date' => Carbon::now(),
                'is_seller_input' => 1
            ], $dataArr);

            $insert = DB::table('kontrak')->insert($data);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    function generateSp(Request $request)
    {
        // Ambil data dari form
        $id_cart_shop       = $request->id_cs;
        $invoice            = $request->invoice;
        $tanggal            = $request->tanggal;
        $catatan            = $request->catatan;
        $content            = $request->content;

        $dataUser = $this->Model['CompleteCartShop']->GetIdSellerAndId_memeber($id_cart_shop);

        $dataArr = [
            'id_complete_cart_shop' => $id_cart_shop,
            'invoice' => $invoice,
            'id_shop' => $this->seller,
            'id_user' => $dataUser->id_user,
            'tanggal_pesan' => $tanggal,
            'catatan' => $catatan,
            'document' => $content,
            'created_at' => Carbon::now(),
        ];

        $check = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->count();

        if ($check > 0) {
            $save = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->update($dataArr);
        } else {
            $save = DB::table('s_pesanan')->insert($dataArr);
        }

        if ($save) {
            return response()->json(['success' => true,]);
        }
        return response()->json(['success' => false,]);
    }

    function downloadKontrak(Request $request)
    {
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

    function downloadSp(Request $request)
    {
        $id_cart_shop = $request->idcs;
        $sp = DB::table('s_pesanan')->where('id_complete_cart_shop', $id_cart_shop)->first();

        if (!$sp) {
            return response()->json(['error' => 'Surat Pesanan tidak ditemukan'], 404);
        }

        $content = $sp->document;
        $imageData = base64_encode(file_get_contents(public_path('img/app/logo-eps-crop.png')));
        $data['logo_src'] = 'data:image/png;base64,' . $imageData;
        $data['content'] = $content;
        $data['current_date'] = date('d F Y H:i:s');

        $htmlContent = view('pdf.v_sp', $data)->render();

        // Setup Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Load HTML content
        $dompdf->loadHtml($htmlContent);

        // Render PDF (optional settings)
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF to string
        $pdfOutput = $dompdf->output();

        // Membuat respons untuk mengunduh file PDF
        return response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="sp_' . time() . '.pdf"',
            'Cache-Control' => 'public, must-revalidate, max-age=0',
            'Pragma' => 'public',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT'
        ]);
    }
}
