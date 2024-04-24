<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use App\Models\Shop;
use App\Models\Menu;
use App\Models\CompleteCartShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    protected $user_id;
    protected $username;
    protected $access_id;
    protected $access_name;
    protected $access_code;
    protected $data;
    protected $menu;

    public function __construct(Request $request)
    {
        // Login
        $this->middleware('admin');
        // menagmbil data dari session
        $this->user_id = $request->session()->get('id');
		$this->username = $request->session()->get('username');
		$this->access_id 	= $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
		$this->access_code 	= $request->session()->get('access_code');
        // Membuat $this->data
        $this->data['title'] = 'Invoice';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_code, 1)->orderBy('urutan')->get();
    }
    
    public function list_inv()
    {
        $datainv = Invoice::with('Finance')->with('Pajak')->get();
        return view('admin.invoice.index', ['datainv' => $datainv, 'menus' => $this->menu], $this->data);
    }

    public function inv_cancelled()
    {
        // Ambil data invoice yang dibatalkan dari database
        $cancelledInvoices = Invoice::where('status', 'cancel')->latest('id')->get();

        // Kirim data invoice yang dibatalkan ke tampilan
        return view('admin.invoice.invoice-cancelled', ['cancelledInvoices' => $cancelledInvoices, 'menus' => $this->menu], $this->data);
    }

    public function detail($id)
    {
        $invoice = Invoice::findOrFail($id);
        $member = Member::where('id', $invoice->id_user)->firstOrFail();
        $cartshop = CompleteCartShop::where('id_cart', $invoice->id)->firstOrFail();
        $shop = Shop::where('id', $cartshop->id_shop)->firstOrFail();

        // mengambil data yang diperlukan saja
        $invoiceData = $invoice->only('invoice');
        $memberData = $member->only('nama');
        $shopData = $shop->only('name');


        return response()->json(['invoice' => $invoiceData, 'member' => $memberData,'shop' => $shopData]);
    }

    public function upload_cancel(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Validasi bahwa request memiliki file yang diunggah
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:2048', // maksimal 2MB
        ]);

        // Ambil file dari request
        $file = $request->file('file');

        // Generate nama unik untuk file
        $fileName = $invoice->invoice . '_cancel' . '.' .$file->getClientOriginalExtension();

        // Simpan file ke direktori storage
        $filePath = $file->storeAs('invoices/cancel', $fileName, 'assets');

        // Di sini Anda dapat menyimpan $publicUrl ke database invoice
        // Contoh:
        // $invoice = Invoice::find($invoiceId);
        $invoice->file_cancel = $filePath;
        $invoice->save();

        return response()->json([
            'message' => 'File berhasil diunggah.',
            'file_url' => $filePath,
        ]);
    }

    public function reupload_cancel(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Hapus file lama jika ada
        if ($invoice->file_cancel) {
            Storage::delete($invoice->file_cancel);
        }
        // Validasi bahwa request memiliki file yang diunggah
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:2048', // maksimal 2MB
        ]);

        // Ambil file dari request
        $file = $request->file('file');

        // Generate nama unik untuk file
        $fileName = $invoice->invoice . '_cancel' . '.' .$file->getClientOriginalExtension();

        // Simpan file ke direktori storage
        $filePath = $file->storeAs('invoices/cancel', $fileName, 'assets');

        // Simpan URL file ke dalam model Invoice
        $invoice->file_cancel = $filePath;
        $invoice->save();

        return response()->json([
            'message' => 'File berhasil diunggah.',
            'file_url' => $filePath,
        ]);
    }

    public function view_cancel($id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->file_cancel) {
            return response()->json(['message' => 'File invoice tidak ditemukan.'], 404);
        }

        // Dapatkan URL file yang telah diunggah
        $fileUrl = asset('storage/' . $invoice->file_cancel);

        return response()->json(['file_url' => $fileUrl]);
    }
}