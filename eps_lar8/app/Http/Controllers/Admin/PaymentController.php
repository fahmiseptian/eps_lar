<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
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
		$this->access_id = $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
		$this->access_code 	= $request->session()->get('access_code');
        // Membuat $this->data
        $this->data['title'] = 'Payment';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_code, 1)->orderBy('urutan')->get();
    }

    public function payment()
    {
        $payment = Payment::where('is_deleted', 0)->get();

        return view('admin.payment.index', ['listpayment' => $payment, 'menus' => $this->menu],  $this->data);
    }

    public function add(Request $request)
    {
        // Validasi request
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:payment_method,code',
            'fee_nominal' => 'required',
            'fee_percent' => 'required',
            'active' => 'required',
            'flag' => 'required',
            'device' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_show' => 'required|boolean', // Pastikan is_show merupakan boolean
        ]);

        // Simpan gambar baru
        $imageName = $request->code . '_' . now()->format('Ymd_His') . '.' . $request->file('image')->getClientOriginalExtension();
        $imagePath = $request->file('image')->storeAs('assets/images/icon/payment', $imageName, 'assets');
        
        // Simpan data ke dalam database
        Payment::create([
            'name' => $request->name,
            'code' => $request->code,
            'fee_nominal' => $request->fee_nominal,
            'fee_percent' => $request->fee_percent,
            'active' => $request->active,
            'flag' => $request->flag,
            'device' => $request->device,
            'image' => $imagePath,
            'is_show' => $request->is_show,
            'created_by' => session()->get('user_id'),
            'is_deleted' => 0,
        ]);

        return response()->json(['message' => 'Payment berhasil ditambahkan'], 200);
    }

    public function detail($id)
    {
        $payment = Payment::findOrFail($id);
        $paymentArray = $payment->toArray(); // Konversi model ke array
        $paymentArray['image_url'] = asset('storage/' . $payment->image); // Tambahkan image_url ke dalam array

        return response()->json($paymentArray);
    }

    public function status($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            // Ubah status payment berdasarkan status awal
            $newStatus = $payment->active === 'Y' ? 'N' : 'Y';
            
            $payment->update(['active' => $newStatus]);
            
            return response()->json(['message' => 'Status payment berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengubah status payment.'], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        // Validasi request
        $request->validate([
            'name' => 'required',
            'code' => 'required',
            'fee_nominal' => 'required',
            'fee_percent' => 'required',
            'flag' => 'required',
            'device' => 'required',
            'is_show' => 'required|boolean', // Pastikan is_show merupakan boolean
        ]);

        // Jika ada gambar yang diunggah
        if ($request->hasFile('image')) {
            // Validasi file gambar
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Hapus gambar lama jika ada
            if ($payment->image) {
                Storage::disk('assets')->delete($payment->image);
            }

            // Simpan gambar baru
            $imageName = $request->code . '_' . now()->format('Ymd_His') . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('assets/images/icon/payment', $imageName, 'assets');
            $payment->image = $imagePath;
            $payment->save();
        }

        // Update data payment
        $payment->update([
            'name' => $request->name,
            'code' => $request->code,
            'fee_nominal' => $request->fee_nominal,
            'fee_percent' => $request->fee_percent,
            'flag' => $request->flag,
            'device' => $request->device,
            'is_show' => $request->is_show,
            'updated_by' => session()->get('user_id'),
            'updated_date' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Payment berhasil diubah'], 200);
    }

    public function delete($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->image) {
            Storage::disk('assets')->delete($payment->image);
        }
        $payment->update([
            'is_deleted' => 1,
            'code' => $payment->code . '_deleted',
            'active' => 'N',
            'is_show' => 0,
            'updated_by' => session()->get('user_id'),
            'updated_date' => Carbon::now(),
        ]);
        return redirect()->back()->with('success', 'Payment berhasil dihapus.');
    }
}