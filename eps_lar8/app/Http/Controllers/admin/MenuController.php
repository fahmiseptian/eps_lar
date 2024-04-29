<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class MenuController extends Controller
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
		$this->access_id = $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
		$this->access_code 	= $request->session()->get('access_code');
        // Membuat $this->data
        $this->data['title'] = 'Menu';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_code, 1)->orderBy('urutan')->get();
    }

    public function menu()
    {
        $menus = Menu::get();
        $parentmenus = Menu::where('status', 1)->get();
        $accesses = Access::where('active', '1')->get();

        return view('admin.menu.index', ['listparent' => $parentmenus, 'listmenu' => $menus, 'accesses' => $accesses, 'menus' => $this->menu],  $this->data);
    }

    public function create()
    {
        $parentmenus = Menu::where('status', 1)->get();
        $accesses = Access::where('active', '1')->get();

        return view('admin.menu.create', ['listparent' => $parentmenus, 'accesses' => $accesses, 'menus' => $this->menu],  $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'icon' => 'required',
            'urutan' => 'required|integer',
        ]);
        
        // Tentukan aturan validasi berdasarkan status
        if ($request->status == 1) {
            // Jika status 1, maka route dan parent_id tidak wajib
            $rules = [
                'route' => '',
                'parent_id' => '',
            ];
        } elseif ($request->status == 2) {
            // Jika status 2, maka route dan parent_id wajib
            $rules = [
                'route' => 'required',
                'parent_id' => 'required',
            ];
        } else {
            // Jika status bukan 1 atau 2, aturan default
            $rules = [
                'route' => '',
                'parent_id' => '',
            ];
        }
        
        // Validasi request berdasarkan aturan yang telah ditentukan
        $request->validate($rules);
        
        // Simpan data ke dalam database
        $menu = Menu::create([
            'nama' => $request->nama,
            'route' => $request->route,
            'icon' => $request->icon,
            'urutan' => $request->urutan,
            'status' => $request->status,
            'parent_id' => $request->parent_id,
        ]);

        // Tambahkan akses terpilih ke menu
        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'access_')) {
                $accessCode = substr($key, 7); // Ambil kode akses dari nama field
                $accessValue = $value === '1' ? 1 : 0; // Ubah value menjadi 1 jika dicentang, 0 jika tidak
                $menu->update([
                    $accessCode => $accessValue,
                ]);
            }
        }

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function detail($id)
    {
        $menu = Menu::findOrFail($id);
        $accesses = Access::where('active', '1')->get();

        return response()->json(['menu' => $menu, 'accesses' => $accesses]);
    }

    public function edit($id)
    {
    // Mendapatkan data menu berdasarkan ID
    $menu = Menu::findOrFail($id);
    $accesses = Access::where('active', '1')->get();

    // Menyimpan data menu yang sudah diedit
    $menu->nama = request('nama');
    $menu->route = request('route');
    $menu->icon = request('icon');
    $menu->urutan = request('urutan');
    $menu->parent_id = request('parent_id');
    $menu->status = request('status');
    
    foreach ($accesses as $access) {
        $menu[$access->code] = request($access->code);
    }

    $menu->save();

    // Mengirimkan respons yang sesuai
    return response()->json(['message' => 'Menu berhasil diubah'], 200);
    }

    public function delete($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }
}
