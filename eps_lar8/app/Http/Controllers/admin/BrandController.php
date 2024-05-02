<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\User;
use App\Models\Menu;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $user_id;
    protected $username;
    protected $name;
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
		$this->name = $request->session()->get('name');
		$this->access_id 	= $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
		$this->access_code 	= $request->session()->get('access_code');
        // Membuat $this->data
        $this->data['title'] = 'Dashboard';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_code, 1)->orderBy('urutan')->get();
    }

    public function index()
    {
        $databrand = Brand::where('status','active')->orderBy('name', 'ASC')->get();
        return view('admin.brand.index', ['databrand'=> $databrand ,'menus' => $this->menu] , $this->data);
    }
}

