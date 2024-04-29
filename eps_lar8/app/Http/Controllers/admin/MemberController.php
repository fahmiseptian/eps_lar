<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Http\Request;

class MemberController extends Controller
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
        if ($request->session()->get('access_code') == null) {
            return redirect()->route('admin.logout');
        }

        // Login
        $this->middleware(['admin', 'activity']);
        // menagmbil data dari session
        $this->user_id = $request->session()->get('id');
		$this->username = $request->session()->get('username');
		$this->access_id 	= $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
		$this->access_code 	= $request->session()->get('access_code');
        // Membuat $this->data
        $this->data['title'] = 'Member';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_code, 1)->orderBy('urutan')->get();
    }
    
    public function index()
    {
        $members = Member::where('member_status', '!=', 'delete')
                        ->orderBy('id', 'desc')
                        ->get();
        return view('admin.member.index', ['members' => $members, 'menus' => $this->menu], $this->data);
    }

    public function show($id)
    {
        $member = Member::findOrFail($id);
        return response()->json(['member' => $member]);
    }

    public function toggleStatus($id)
    {
        try {
            $member = Member::findOrFail($id);
            
            // Ubah status anggota berdasarkan status awal
            $newStatus = $member->member_status === 'active' ? 'suspend' : 'active';
            
            $member->update(['member_status' => $newStatus]);
            
            return response()->json(['message' => 'Status anggota berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengubah status anggota.'], 500);
        }
    }

    public function delete($id)
    {
        $member = Member::findOrFail($id);
        $member->update(['member_status' => 'delete', 'registered_member' => 0]);
        return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
    }
}
