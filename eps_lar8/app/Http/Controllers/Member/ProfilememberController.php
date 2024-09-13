<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ProfilememberController extends Controller
{
    protected $data;
    protected $model;
    public function __construct(Request $request)
    {
        // Ambil semua data sesi
        $sessionData = $request->session()->all();
        $this->data['id_user'] = $sessionData['id'] ?? null;
        $this->model['member'] = new Member();
        $this->data['nama_user'] = '';

        if ($this->data['id_user'] != null) {
            $this->data['member'] = $this->model['member']->find($this->data['id_user']);
            $this->data['nama_user'] = $this->data['member']->nama;
        }
    }

    // Metode lain dalam controller
    public function index() {
        return view('member.profile.index',$this->data);
    }

    public function dashboard() {
        return view('member.profile.dashboard',$this->data);
    }
}
