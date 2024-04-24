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
    protected $access_id;
    protected $access_name;
    protected $data;
    protected $menu;

    public function __construct(Request $request)
    {
        // Login
        $this->middleware('admin');
        // menagmbil data dari session
        $this->user_id = $request->session()->get('id');
		$this->username = $request->session()->get('username');
		$this->access_id 	= $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
        // Membuat $this->data
        $this->data['title'] = 'Dashboard';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_name, 1)->orderBy('urutan')->get();
    }

    public function index()
    {
        $databrand = Brand::where('status','active')->orderBy('name', 'ASC')->get();
        return view('admin.brand.index', ['databrand'=> $databrand ,'menus' => $this->menu] , $this->data);
    }
}

