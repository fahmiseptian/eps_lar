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
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
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

        $this->Model['saldo'] = new Saldo();
        $this->Model['Rekening'] = new Rekening();
        $this->Model['shop'] = new Shop();

        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   =  $this->Model['saldo']->calculatePendingSaldo($this->seller);
        $saldoSuccess   =  $this->Model['saldo']->calculateSuccessSaldo($this->seller);
        $rekening       = $this->Model['Rekening']->getDefaultRekeningByShop($this->seller);
        $rekNdefault    = $this->Model['Rekening']->JumlahRekeningIsDefaultN($this->seller);
        $this->verificationService = new VerificationService;

        // Membuat $this->data
        $this->data['title'] = 'Finance';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;
        $this->data['saldoSelesai'] = $saldoSuccess;
        $this->data['rekening'] = $rekening;
        $this->data['jmlRekening'] = $rekNdefault;

        $this->Model['Penarikan_Dana'] = new PenarikanDana();
    }

    public function index()
    {
        return view('seller.finance.index');
    }

    public function showPenghasilan()
    {
        $Pendingsaldo   = $this->Model['saldo']->Pendingsaldo($this->seller);
        return response()->json(['data' => $this->data, 'Pendingsaldo' => $Pendingsaldo]);
    }

    function Successsaldo()
    {
        $Successsaldo   = $this->Model['saldo']->Successsaldo($this->seller);
        return response()->json(['data' => $this->data, 'Pendingsaldo' => $Successsaldo]);
    }

    function PenarikanDana()
    {
        $PenarikanDana   = $this->Model['Penarikan_Dana']->getPenarikanDana($this->seller);
        return response()->json(['data' => $this->data, 'PenarikanDana' => $PenarikanDana]);
    }

    public function showSaldo()
    {
        $PenarikanDana   = $this->Model['Penarikan_Dana']->getPenarikanDana($this->seller);
        return view('seller.finance.saldo', $this->data, ['saldo' => $PenarikanDana]);
    }

    public function showRekening()
    {
        $allBanks           = Bank::all();
        $rekeningNotdefault = Rekening::getRekeningByShopAndIsDefaultN($this->seller);
        return view('seller.finance.rekening', $this->data, ['rekeningNotdefault' => $rekeningNotdefault, 'Banks' => $allBanks]);
    }

    function getbank()
    {
        $allBanks           = Bank::all();
        return response()->json($allBanks);
    }

    function rekeningNotdefault()
    {
        $rekeningNotdefault = $this->Model['Rekening']->getRekeningByShopAndIsDefaultN($this->seller);
        return response()->json(['data' => $this->data, 'rekeningNotdefault' => $rekeningNotdefault]);
    }

    public function showPembayaran()
    {
        return view('seller.finance.pembayaran', $this->data);
    }

    public function getRekeningById($id)
    {
        $rekeningbyId       = Rekening::getRekeningById($id, $this->seller);
        return response()->json(['data_rekening_seller' => $rekeningbyId]);
    }

    public function updateRekening(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:rekening,id',
            'rek_owner' => 'required|string|max:255',
            'rek_location' => 'required|string|max:255',
            'rek_city' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil data rekening berdasarkan ID yang diberikan
        $rekening = Rekening::where('id', $request->input('id'))
            ->where('id_shop', $this->seller) // Gantilah dengan ID shop yang sesuai
            ->first();

        // Perbarui data rekening
        if ($rekening) {
            $rekening->rek_owner = $request->input('rek_owner');
            $rekening->rek_location = $request->input('rek_location');
            $rekening->rek_city = $request->input('rek_city');

            $rekening->save();

            return response()->json([
                'success' => true,
                'message' => 'Data rekening berhasil diperbarui',
                'rekening' => $rekening
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan atau Anda tidak memiliki izin untuk mengubah data rekening ini'
            ], 404);
        }
    }
    public function deleteRekening($id)
    {
        $rekening = Rekening::where('id', $id)
            ->where('id_shop', $this->seller)
            ->first();
        if ($rekening) {
            $rekening->is_deleted = 'Y';
            $rekening->save();
            return response()->json(['success' => true, 'message' => 'Rekening berhasil dihapus']);
        } else {
            return response()->json(['success' => false, 'message' => 'Rekening tidak ditemukan']);
        }
    }

    public function addRekening(Request $request)
    {
        // Validasi data dari request
        $request->validate([
            'nama' => 'required|string|max:255',
            'bank' => 'required|integer|exists:bank,id',
            'noRekening' => 'required|string|max:20',
            'cabangBank' => 'required|string|max:255',
            'kotaKabupaten' => 'required|string|max:255',
        ]);

        $update = DB::table('rekening')->where('id_shop', $this->seller)->update(['is_default' => 'N']);

        // Buat data rekening baru
        $rekening = new Rekening([
            'rek_owner' => $request->nama,
            'id_bank' => $request->bank,
            'rek_number' => $request->noRekening,
            'rek_location' => $request->cabangBank,
            'rek_city' => $request->kotaKabupaten,
            'id_shop' => $this->seller,
            'is_default' => 'Y',
            'created_dt' => Carbon::now()
        ]);

        // Simpan rekening baru
        $rekening->save();

        // Kirim respons sukses
        return response()->json(['success' => true]);
    }

    public function updateDefaultRekening(Request $request)
    {
        // Ambil ID rekening dari permintaan
        $rekeningId = $request->input('id');
        $idShop = $this->seller;

        // Lakukan pembaruan dalam transaksi untuk menjaga konsistensi data
        DB::transaction(function () use ($rekeningId, $idShop) {
            Rekening::where('id_shop', $idShop)
                ->where('is_default', 'Y')
                ->update(['is_default' => 'N']);

            Rekening::where('id', $rekeningId)
                ->where('id_shop', $idShop)
                ->update(['is_default' => 'Y']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Rekening utama berhasil diubah'
        ]);
    }

    public function sendVerificationCode(Request $request)
    {
        $id_userbyShop = Shop::where('id', $this->seller)
            ->value('id_user');
        $email = Member::where('id', $id_userbyShop)
            ->value('email');
        $result = $this->verificationService->sendVerificationCode($email);
        return response()->json($result);
    }

    public function verifyCode(Request $request)
    {
        $id_userbyShop = Shop::where('id', $this->seller)
            ->value('id_user');
        $email = Member::where('id', $id_userbyShop)
            ->value('email');
        $code = $request->input('code');
        $isValid = $this->verificationService->verifyCode($email, $code);

        if ($isValid) {
            return response()->json(['message' => 'Kode verifikasi valid']);
        } else {
            return response()->json(['message' => 'Kode verifikasi tidak valid'], 400);
        }
    }

    public function updatePin(Request $request)
    {
        $id_userbyShop = Shop::where('id', $this->seller)
            ->value('id_user');
        $email = Member::where('id', $id_userbyShop)
            ->value('email');
        $newPin = $request->input('new_pin');

        $result = $this->verificationService->updateNewPin($id_userbyShop, $newPin);
        return response()->json($result);
    }

    public function savePin(Request $request)
    {
        // Validasi input PIN
        $request->validate([
            'newPin' => 'required|string|size:6',
        ]);

        // Enkripsi PIN baru
        $newPin = $request->input('newPin');
        $encodedPin = base64_encode($newPin);

        // Update PIN di tabel shop
        try {
            $update = DB::table('shop')->where('id', $this->seller)->update([
                'pin_saldo' => $encodedPin
            ]);

            if ($update) {
                return response()->json(['message' => 'Berhasil Memperbaharui PIN'], 200);
            } else {
                Log::error('Failed to update PIN: No rows affected', ['seller_id' => $this->seller]);
                return response()->json(['message' => 'Gagal Memperbaharui PIN'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update PIN', ['error' => $e->getMessage(), 'seller_id' => $this->seller]);
            return response()->json(['message' => 'Gagal Memperbaharui PIN'], 500);
        }
    }

    function getTraxPending()
    {
        $trx = $this->Model['saldo']->getRevenuePending($this->seller);
        return response()->json(['trx' => $trx]);
    }

    function RequestRevenue(Request $request)
    {
        $pin        = base64_encode($request->pin);
        $ids_trx    = $request->idTrx;

        // Checking
        $checkPin   = $this->Model['shop']->getPinSaldo($this->seller);
        $rekening   = Rekening::getDefaultRekeningByShop($this->seller);

        if ($checkPin == null) {
            return response()->json(['message' => 'Anda Belum Membuat PIN'], 500);
        }

        if ($checkPin != $pin) {
            return response()->json(['message' => 'PIN salah'], 500); // Adjusted to give more meaningful message
        }

        if ($rekening == null) {
            return response()->json(['message' => 'Anda Belum Memiliki Rekening'], 500);
        }

        $action = $this->Model['saldo']->requestRevenue($this->seller, $ids_trx);

        return response()->json(['success' => true, 'action' => $action], 200);
    }

    function getDetailPenarikan($id_trx){
        $data = $this->Model['Penarikan_Dana']->detail_penarikan($id_trx, $this->seller);
        return response()->json($data);
    }
}
