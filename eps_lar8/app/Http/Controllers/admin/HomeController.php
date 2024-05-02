<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Member;
use App\Models\Menu;
use Illuminate\Http\Request;

class HomeController extends Controller
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
        $jmlhproduct = Product::count();
        $this->data['jmlhproduct'] = $jmlhproduct;

        $shop = Shop::where('status', 'active')->count();
        $this->data['shop'] = $shop;

        $member = Member::where('member_status', 'active')->count();
        $this->data['member'] = $member;

        return view('admin.home.index', ['menus' => $this->menu] , $this->data);
    }
}

