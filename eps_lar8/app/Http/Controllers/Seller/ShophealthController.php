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
use App\Models\Violation;

class ShophealthController extends Controller
{
    protected $user_id;
    protected $username;
    protected $seller;
    protected $data;
    protected $menu;
    protected $verificationService;

    public function __construct(Request $request)
    {
        $this->seller 	= $request->session()->get('seller_id');

        $sellerType     = Shop::getTypeById($this->seller);
        $saldoPending   = Saldo::calculatePendingSaldo($this->seller);
        $saldoSuccess   = Saldo::calculateSuccessSaldo($this->seller);
        $this->verificationService = new VerificationService;
        
        // Membuat $this->data
        $this->data['title'] = 'Shop Health';
        $this->data['seller_type'] = $sellerType;
        $this->data['saldo'] = $saldoPending;
        $this->data['saldoSelesai'] = $saldoSuccess;
    }

    public function index()
    {
        $chatDetailInstance = new Chatdetail();

        // Kesehatan Toko
        $this->data['pelanggaran_produk_berat'] = Violation::countViolation($this->seller,"berat");
		$this->data['produk_spam'] = Violation::countViolation($this->seller, null, ['pv.id' => '7']);
		$this->data['produk_imitasi'] = Violation::countViolation($this->seller, null, ['pv.id' => '15']);
		$this->data['produk_yang_dilarang'] = Violation::countViolation($this->seller, null, ['pv.id' => '4']);
		$this->data['pelanggaran_produk_ringan'] = Violation::countViolation($this->seller, "ringan");
		$this->data['count_order'] = CompleteCartShop::countAllorder($this->seller);
        $this->data['tidak_terselesaikan'] = CompleteCartShop::get_count_order($this->seller, 'cancel_by_time');
		$this->data['pembatalan'] = CompleteCartShop::get_count_order($this->seller, 'cancel_by_seller');
		$this->data['pengembalian'] =CompleteCartShop::get_count_order($this->seller, 'refund');
		$this->data['pengemasan'] = CompleteCartShop::get_count_order($this->seller, 'on_packing_process');
        $this->data['total_chat_time'] =  $chatDetailInstance->getChatSummary($this->seller);
        $this->data['total_percentage_chat'] =  $chatDetailInstance->get_percentage_chat($this->seller);

        return view('seller.health.index',$this->data);
    }
}