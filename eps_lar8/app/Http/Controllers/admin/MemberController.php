<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\member;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    protected $user_id;
    protected $username;
    protected $access_id;
    protected $data;

    public function __construct(Request $request)
    {
        // Login
        $this->middleware('admin');
        // menagmbil data dari session
        $this->user_id = $request->session()->get('id');
		$this->username = $request->session()->get('username');
		$this->access_id 	= $request->session()->get('access_id');
        // Membuat $this->data
        $this->data['title'] = 'Member';
        $this->data['profile'] = User::find($this->access_id);
    }
    
    public function index()
    {
        $members = Member::where('member_status', '!=', 'delete')
                        ->orderBy('id', 'desc')
                        ->get();
        return view('admin.member.index', compact('members'));
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
