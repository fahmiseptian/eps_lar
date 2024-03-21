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
		$this->access_id 	= $request->session()->get('access_id');
        // Membuat $this->data
        $this->data['title'] = 'Dashboard';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->orderBy('urutan')->get();
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

