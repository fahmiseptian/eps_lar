<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ProfilememberController extends Controller
{
    protected $data;
    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
    }

    // Metode lain dalam controller
    public function index($id) {
        return view('member.profile.index',$this->data);
    }
}
