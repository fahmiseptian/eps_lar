<?php

namespace App\Libraries;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class Checkout
{
    protected $model;

    public function __construct()
    {
        $this->model = new Cart();
    }

    function keranjang($id_member)
    {
        $query = DB::table('cart as c')
            ->select(
                'c.*'
            )
            ->where('c.id_user', $id_member)
            ->where('c.status', 'pending')
            ->where('c.complete_checkout', 'N')
            ->first();

        // if ($query->id_address_user == 0) {
        //     $id_address = DB::table('member_address')
        //         ->where('member_id', $id_member)
        //         ->where('is_default_shipping', 'yes')
        //         ->value('member_address_id');

        //     $update = Cart::where('id', $query->id)->update(['id_address_user' => $id_address]);
        // }

        return $query;
    }
}
