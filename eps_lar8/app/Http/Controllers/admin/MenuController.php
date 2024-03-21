<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\User;

class MenuController extends Controller
{
    protected $user_id;
    protected $username;
    protected $access_id;
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
        // Membuat $this->data
        $this->data['title'] = 'Menu';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->orderBy('urutan')->get();
    }

    public function create()
    {
        return view('admin.menu.index', ['menus' => $this->menu],  $this->data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'route' => 'required',
            'icon' => 'required',
            'urutan' => 'required|integer',
        ]);

        Menu::create([
            'nama' => $request->nama,
            'route' => $request->route,
            'icon' => $request->icon,
            'urutan' => $request->urutan,
            'status' => $request->status ?? 1,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.menu.create')->with('success', 'Menu berhasil ditambahkan.');
    }
}
