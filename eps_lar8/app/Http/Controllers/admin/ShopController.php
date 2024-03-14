<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Member;
use App\Models\Lpse_config;
use App\Models\Product;
use Illuminate\Http\Request; 

class ShopController extends Controller
{
    public function shop()
    {
        $datashop = Shop::where('status', '!=', 'delete')
                        ->orderBy('id', 'desc')
                        ->get();

        return view('admin.shop.index', ['datashop' => $datashop]);
    }

    public function detail($id)
    {
        $shop = Shop::findOrFail($id);
        $member = Member::where('id', $shop->id_user)->firstOrFail();

        return response()->json([ 'shop' => $shop, 'member' => $member]);
    }

    public function updateStatus($id)
    {
        try {
            $shop = Shop::findOrFail($id);
            // Ubah status anggota berdasarkan status awal
            $newStatus = $shop->status === 'active' ? 'inactive' : 'active';
            
            $shop->update(['status' => $newStatus]);
            
            return response()->json(['message' => 'Status anggota berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['error' => $newStatus ], 500);
        }
    }
    
    public function updateTypeUp($id)
{
    try {
        $shop = Shop::findOrFail($id);
        
        // Mengatur perubahan tipe toko berdasarkan arah yang diberikan
        $newType = '';
        switch ($shop->type) {
            case 'silver':
                $newType = 'gold';
                break;
            case 'gold':
                $newType = 'platinum';
                break;
            case 'platinum':
                $newType = 'trusted_seller';
                break;
            case 'trusted_seller':
                // Jika sudah trusted_seller, tidak bisa naik lagi
                return response()->json(['message' => 'Teratas']);
                break;
            default:
                // Tindakan default jika tipe tidak cocok dengan salah satu kondisi di atas
                break;
        }
        
        // Jika $newType tidak kosong, update tipe toko
        if ($newType !== '') {
            $shop->update(['type' => $newType]);
            return response()->json(['message' => $newType]);
        } else {
            // Tindakan jika tidak ada perubahan tipe yang dilakukan
            return response()->json(['message' => 'Tidak ada perubahan tipe yang dilakukan.']);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function updateTypeDown($id)
{
    try {
        $shop = Shop::findOrFail($id);
        
        // Mengatur perubahan tipe toko berdasarkan arah yang diberikan
        $newType = '';
        switch ($shop->type) {
            case 'silver':
                // Jika sudah silver, tidak bisa turun lagi
                return response()->json(['message' => 'Terbawah']);
                break;
            case 'gold':
                $newType = 'silver';
                break;
            case 'platinum':
                $newType = 'gold';
                break;
            case 'trusted_seller':
                $newType = 'platinum';
                break;
            default:
                // Tindakan default jika tipe tidak cocok dengan salah satu kondisi di atas
                break;
        }
        
        // Jika $newType tidak kosong, update tipe toko
        if ($newType !== '') {
            $shop->update(['type' => $newType]);
            return response()->json(['message' => $newType]);
        } else {
            // Tindakan jika tidak ada perubahan tipe yang dilakukan
            return response()->json(['message' => 'Tidak ada perubahan tipe yang dilakukan.']);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function delete($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->update(['status' => 'delete']);
        $member = Member::where('id', $shop->id_user)->firstOrFail();
        $member->update(['member_status' => 'delete', 'registered_member' => 0]);
        return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
    }

    public function lpse_config()
    {
        $datashop = Shop::where('status', '!=', 'delete')
                        ->orderBy('id', 'desc')
                        ->get();

        return view('admin.shop.lpse-config', ['datashop' => $datashop]);
    }

    public function updateIsTop($id) {
        try {
            $shop = Shop::findOrFail($id);
            $newTOP = $shop->is_top === '1' ? '0' : '1';
            $shop->update(['is_top' => $newTOP]);

            return response()->json(['is_top' => $shop->is_top]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function formulaLpse()
    {
        $lpse = Lpse_config::orderBy('id', 'desc')
                        ->first();  

        $formula = $lpse->only('ppn','pph','fee_mp_percent');
        return response()->json(['formula' => $formula]);
    }


    public function updateFormula(Request $request)
    {
        // Validasi permintaan
        $request->validate([
            'pph' => 'required',
            'ppn' => 'required',
            'fee_mp_percent' => 'required',
        ]);

        // Update formula di database
        $formula = Lpse_config::first();
        $formula->pph = $request->pph;
        $formula->ppn = $request->ppn;
        $formula->fee_mp_percent = $request->fee_mp_percent;
        $formula->save();

        return response()->json(['message' => 'Formula updated successfully']);
    }


    // Mengambil produk dengan id shop
    public function getProduct($id)
    {
        $products = Product::where('id_shop', $id)->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'Tidak ada produk yang ditemukan untuk toko dengan ID yang diberikan'], 404);
        }
        return response()->json(['products' => $products]);
    }

    // update status product
    public function updateProduct($id)
    {
        try {
            $product = Product::find($id);
            $newStatus = $product->status_lpse === '1' ? '0' : '1';
            $product->update(['status_lpse' => $newStatus]);

            return response()->json(['status_lpse' => "sasasa"]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

