<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class Notification {
    function getProductReview($id_shop, $where = null) {
        $query = DB::table('product_review as pr')
            ->select('pr.*', 'm.nama as nama_member', 'p.name', 'pi.image50 as image_product')
            ->leftJoin('products as p', 'p.id', '=', 'pr.id_product')
            ->leftJoin('product_image as pi', 'pi.id_product', '=', 'p.id')
            ->leftJoin('member as m', 'm.id', '=', 'pr.id_user')
            ->where('p.id_shop', $id_shop)
            ->where('pi.is_default', 'yes');

        if ($where != null) {
            $query->where($where);
        }

        $data_review = $query->get();

        return $data_review;
    }

    function getFavorite($id_shop, $where = null) {
        $query = DB::table('member_wishlist as mw')
            ->select('mw.*', 'm.nama as nama_member', 'p.name', 'pi.image50 as image_product')
            ->leftJoin('product as p', 'p.id', '=', 'mw.id_product')
            ->leftJoin('product_image as pi', 'pi.id_product', '=', 'p.id')
            ->leftJoin('member as m', 'm.id', '=', 'mw.id_member')
            ->where('p.id_shop', $id_shop)
            ->where('p.status_delete', 'N')
            ->where('pi.is_default', 'yes');

        if ($where != null) {
            $query->where($where);
        }

        $data_favorite = $query->get();

        return $data_favorite;
    }

    function getNotificationOrder($id_shop, $where = null)
    {
        $query = DB::table('complete_cart_shop as ccs')
            ->select('ccs.*', 'cc.invoice')
            ->leftJoin('complete_cart as cc', 'cc.id', '=', 'ccs.id_cart')
            ->where('ccs.id_shop', $id_shop);

        if ($where != null) {
            $query->where($where);
        }

        $query->orderBy('ccs.last_update', 'DESC');

        $order = $query->get();

        return $order;
    }

    function getNotificationPromo($id_shop, $where = null)
    {
        $query = DB::table('promo_product as pp')
            ->select('pp.*', 'p.name', 'p.id as product_id', 'pi.image50 as image_product')
            ->leftJoin('product as p', 'p.id', '=', 'pp.id_product')
            ->leftJoin('product_image as pi', 'pi.id_product', '=', 'p.id')
            ->where('pp.id_shop', $id_shop)
            ->where('p.status_delete', 'N')
            ->where('pi.is_default', 'yes');

        if ($where != null) {
            $query->where($where);
        }

        $query->orderBy('pp.last_update', 'DESC');

        $order = $query->get();

        return $order;
    }

    public function getNotificationVoucher($id_shop, $where = null)
    {
        $array_coupon = $this->_getNotificationVoucher($id_shop);

        if (count($array_coupon) > 0) {
            $query = DB::table('complete_cart_shop as ccs')
                ->select('c.code', 'ccs.*', 'cc.invoice')
                ->leftJoin('complete_cart as cc', 'cc.id', '=', 'ccs.id_cart')
                ->leftJoin('coupon as c', 'c.id', '=', 'ccs.id_coupon')
                ->where('ccs.id_shop', $id_shop)
                ->whereIn('ccs.id_coupon', $array_coupon);

            if ($where != null) {
                $query->where($where);
            }

            $coupon_usage = $query->get();

            return $coupon_usage;
        } else {
            return [];
        }
    }


    private function _getNotificationVoucher($id_shop)
    {
        $coupon = DB::table('coupon')
            ->select('id')
            ->where('id_shop', $id_shop)
            ->pluck('id')
            ->toArray();

        return $coupon;
    }

}
