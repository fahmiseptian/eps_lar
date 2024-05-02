<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_profile;
use App\Models\Access;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserController extends Controller
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
		$this->access_id = $request->session()->get('access_id');
		$this->access_name 	= $request->session()->get('access_name');
		$this->access_code 	= $request->session()->get('access_code');
        // Membuat $this->data
        $this->data['title'] = 'User';
        $this->data['profile'] = User::find($this->access_id);

        $this->menu = Menu::where('status', 1)->where($this->access_code, 1)->orderBy('urutan')->get();
    }

    public function user()
    {
        $users = User::with('Access')->where('active', '!=', 2)->get();
        $accesses = Access::where('active', '1')->get();

        return view('admin.user.index', ['users' => $users, 'accesses' => $accesses, 'menus' => $this->menu],  $this->data);
    }

    public function add_access(Request $request)
    {
        // Validasi input, pastikan 'name' tidak kosong
        $request->validate([
            'name' => 'required|string', // Atur sesuai dengan kebutuhan validasi Anda
        ]);
        
        // Simpan data ke dalam database
        $access = Access::create([
            'name' => $request->name,
            'code' => Str::slug($request->name, '_'),
            'active' => 1,
            'created_by' => session()->get('user_id'),
        ]);

        // Perbarui kolom 'menu_name' di tabel 'ad_menu'
        DB::statement("ALTER TABLE ad_menu ADD COLUMN {$access->code} INT");

        // Update menu dashboard akan otomatis aktif untuk akses baru
        Menu::where('nama', 'Dashboard')->update([
            $access->code => 1,
        ]);

        return redirect()->route('admin.user')->with('success', 'Akses berhasil ditambahkan.');
    }

    public function edit_access(Request $request, $id)
    {
        // Mencari data access berdasarkan ID
        $access = Access::findOrFail($id);

        try {
            $updated = $access->update([
                'name' => $request->name,
                'code' => Str::slug($request->name, '_'),
                'updated_by' => session()->get('user_id'),
                'updated_date' => Carbon::now(),
            ]);

             // Jika data berhasil diubah, perbarui kolom 'menu_name' di tabel 'ad_menu'
            if ($updated) {
                DB::statement("ALTER TABLE ad_menu CHANGE COLUMN {$request->old_code} {$access->code} INT");
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return redirect()->back()->with('success', 'Data akses berhasil diubah.');
    }

    public function getAvailableAccess($id)
    {
        // Mengambil akses yang tersedia dengan status aktif (active = 1) dan bukan akses yang akan dihapus
        $accesses = Access::where('active', 1)
            ->where('id', '!=', $id) // Tidak termasuk akses yang akan dihapus
            ->get();

        return response()->json(['accesses' => $accesses]);
    }

    public function delete_access($id)
    {
        // Mencari data access berdasarkan ID
        $access = Access::findOrFail($id);

        // Mendapatkan akses pengganti dari request
        $replacementAccessId = request('replacement_access');

        try {
            $access->update([
                'active' => 0,
                'deleted_by' => session()->get('user_id'),
                'deleted_date' => Carbon::now(),
            ]);

            // Update users yang memiliki access_id sama dengan $id menjadi $replacementAccessId
            User::where('access_id', $id)->where('active', '!=', 2)->update([
                'access_id' => $replacementAccessId,
                'updated_by' => session()->get('user_id'),
                'updated_date' => Carbon::now(),
            ]);

            // Jika data berhasil dihapus, hapus kolom 'menu_name' di tabel 'ad_menu'
            DB::statement("ALTER TABLE ad_menu DROP COLUMN {$access->code}");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return redirect()->back()->with('success', 'Akses berhasil dihapus.');
    }

    public function add()
    {
        $accesses  = Access::where('active', 1)->get();

        return view('admin.user.add', ['accesses' => $accesses, 'menus' => $this->menu],  $this->data);
    }

    public function store(Request $request)
    {
        $encryptedPassword = User::encryptPassword($request->password);

        // Simpan data ke dalam database
        $user = User::create([
            'username' => $request->username,
            'password' => $encryptedPassword,
            'access_id' => $request->access_id,
            'active' => $request->active,
            'created_by' => session()->get('user_id'),
        ]);

        $user_id = $user->id;

        User_profile::create([
            'user_id' => $user_id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'seoname' => strtolower($request->firstname) . '-' . strtolower($request->lastname),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.user')->with('success', 'User berhasil ditambahkan.');
    }

    public function detail($id)
    {
        $user = User::with('Access')->findOrFail($id);
        $profile = User_profile::findOrFail($id);
        $accesses  = Access::where('active', 1)->get();

        return response()->json(['user' => $user, 'profile' => $profile, 'accesses' => $accesses]);
    }
    
    public function edit(Request $request, $id)
    {
        // Mencari data user dan profile berdasarkan ID
        $user = User::findOrFail($id);
        $profile = User_profile::findOrFail($id);

        if ($request->has('username') || $request->has('access_id') || $request->has('active') || $request->has('password')) {
            // Jika permintaan mengandung data pengguna, update data pengguna
            try {
                if ($request->password !== '' && $request->password !== null) {
                    $user->update([
                        'password' => $user->encryptPassword($request->password)
                    ]);
                }
                $user->update([
                    'username' => $request->username,
                    'access_id' => $request->access_id,
                    'active' => $request->active,
                    'updated_by' => session()->get('user_id'),
                    'updated_date' => Carbon::now(),
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        
        if ($request->has('firstname') || $request->has('lastname')) {
            // Jika permintaan mengandung data profil pengguna, update data profil pengguna
            try {
                $profile->update([
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'seoname' => strtolower($request->firstname) . '-' . strtolower($request->lastname),
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['message' => 'Data user berhasil diubah'], 200);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $profile = User_profile::findOrFail($id);

        try {
            $user->update([
                'active' => 2,
                'deleted_by' => session()->get('user_id'),
                'deleted_date' => Carbon::now(),
            ]);
            $profile->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

}
