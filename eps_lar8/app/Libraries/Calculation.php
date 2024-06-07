<?php
namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class Calculation
{
    protected $data;
    function __construct() {
        $this->data['config'] = DB::table('lpse_config')->select('*')->first();
    }

    function OngkirAwal($ongkir_dasar) {
        $config = $this->data['config'];
        $ppn_percent = $config->ppn / 100;
        $pph_percent = $config->pph / 100;

        // Menghitung PPN dan PPh
        $ppn_ongkir = $ppn_percent * $ongkir_dasar;
        $pph_ongkir = $pph_percent * $ongkir_dasar;

        // Menjumlahkan ongkir dasar dengan PPN dan PPh
        $total_ongkir = $ongkir_dasar + $ppn_ongkir + $pph_ongkir;

        // Membulatkan ke atas ke kelipatan terdekat dari 100
        $ongkir_sudah_ppn_dan_pph = ceil($total_ongkir / 100) * 100;

        $result = [
            "ongkir_sudah_ppn_dan_pph" => $ongkir_sudah_ppn_dan_pph,
            "ppn_ongkir" => $ppn_ongkir,
            "pph_ongkir" => $pph_ongkir
        ];

        return $result;
    }

    function OngkirSudahPPN($ongkir_dasar) {
        $config = $this->data['config'];
        $ppn_percent = $config->ppn / 100;
        $pph_percent = $config->pph / 100;

        $ongkir = $this->OngkirAwal($ongkir_dasar);
        $ongkir_sudah_ppn_dan_pph = $ongkir["ongkir_sudah_ppn_dan_pph"];

        $ppn_ongkir = round($ongkir_sudah_ppn_dan_pph * $ppn_percent);
        $pph_ongkir = round($ongkir_sudah_ppn_dan_pph * $pph_percent);

        $ongkir_setelah_ppn = $ppn_percent * $ongkir_sudah_ppn_dan_pph;
        $ongkir_akhir =  $ongkir_sudah_ppn_dan_pph + $ongkir_setelah_ppn;

        $result = [
            "base_price" => $ongkir_dasar,
            "ppn_ongkir" => $ppn_ongkir,
            "pph_ongkir" => $pph_ongkir,
            "ongkir_sudah_ppn_dan_pph" => $ongkir_sudah_ppn_dan_pph,
            "Ongkir_akhir" => $ongkir_akhir
        ];


        return $result;
    }

    public function calcShippingInsuranceCost($dataArr, $is_insurance = false)
    {
        $config = $this->data['config'];
        $int_rounded = 100;

        $id_shipping = $dataArr['id_shipping'] ?? null;
        $id_courier = $dataArr['id_courier'] ?? null;
        $sum_price = $dataArr['sum_price'];

        $final_price = 0;
        $final_price_ppn = 0;
        $final_price_pph = 0;

        $ppn = $dataArr['ppn'] ?? $config->ppn;
        $pph = $dataArr['pph'] ?? $config->pph;

        $ppn = ($ppn / 100);
        $pph = ($pph / 100);

        if ($is_insurance) {
            $query = DB::table('courier as a')
                ->select('a.id', 'a.code', 'a.name', 'a.max_weight', 'a.insurance_fee_percent', 'a.insurance_fee_nominal')
                ->join('shipping as b', 'b.id_courier', '=', 'a.id');

            if (!empty($id_shipping)) {
                $query->where('b.id', $id_shipping);
            } else {
                $query->where('a.id', $id_courier);
            }

            // Menghapus klausa groupBy
            // Menggunakan distinct untuk memastikan tidak ada duplikasi entri
            $data = $query->distinct()->first();

            if ($data) {
                $fee_percent = ($data->insurance_fee_percent / 100);
                $fee_nominal = $data->insurance_fee_nominal;
                $insurance_nominal_ = round($sum_price * $fee_percent);

                $insurance_nominal_calc = round($insurance_nominal_) + $fee_nominal;
                $insurance_nominal_ppn = round($insurance_nominal_calc * $ppn);
                $insurance_nominal_pph = round($insurance_nominal_calc * $pph);

                // NOTE Total Insurance exclude PPN
                $final_price = $insurance_nominal_calc + $insurance_nominal_ppn + $insurance_nominal_pph;
            }

            // NOTE Final Price exclude PPN
            $final_price = ceil($final_price / $int_rounded) * $int_rounded;

            // NOTE Include PPN & PPH
            $final_price_ppn = round($final_price * (1 + $ppn));
            $final_price_pph = round($final_price * (1 + $pph));
        }

        return [
            'base_price' => $insurance_nominal_calc ?? 0,
            'price' => $final_price,
            'price_ppn' => $final_price_ppn,
            'price_pph' => $final_price_pph,
        ];
    }

    function calc_handling_cost($dataArr) {
        $config = $this->data['config'];
        // NOTE Kalkulasi Handling Cost / Biaya Penanganan Payment Gateway

        $int_rounded = 1000;
        $total_ppn = 0;
        $subtotal_exclude_ppn = 0;
        $subtotal_exclude_ppn2 = 0;

        $fee_nominal = $dataArr['fee_nominal'] ?? 0;
        $fee_percent = (($dataArr['fee_percent'] ?? 0) / 100);

        // FIXME ambil data sum_price (total price product ppn + non ppn)
        // FIXME ambil data sum_price_ppn_only (total price product ppn only)
        $sum_price = $dataArr['sum_price'] ?? 0;
        $sum_price_ppn_only = $dataArr['sum_price_ppn_only'] ?? 0;
        $sum_shipping = $dataArr['sum_shipping'] ?? 0;
        $total_ppn_ = $dataArr['total_ppn'] ?? 0;
        $total_non_ppn = $dataArr['total_non_ppn'] ?? 0;

        if (!isset($dataArr['ppn'])) {
            $ppn = $config['ppn'];
        } else {
            $ppn = $dataArr['ppn'];
        }
        $fee_mp_percent = $dataArr['fee_mp_percent'] ?? $config->fee_mp_percent;


        $fee_mp_percent = ($fee_mp_percent / 100);

        $ppn = ($ppn / 100);


        if (!empty($total_non_ppn)) {
            $subtotal_exclude_ppn = $total_non_ppn;
        } else {
            // $sum_price_ppn_only = ($sum_price_ppn_only / (1 + $ppn));
            $subtotal_exclude_ppn = ($sum_price_ppn_only + $sum_shipping + $total_ppn_);

            // NOTE for subtotal all (product non ppn + product ppn only + shipping)
            $subtotal_exclude_ppn2 = round($sum_price + $sum_shipping + $total_ppn_);
        }

        $subtotal_exclude_ppn = round($subtotal_exclude_ppn);

        // Formula
        if (empty($total_ppn_)) {
            $ppn_total = round($subtotal_exclude_ppn * $ppn);
        } else {
            $ppn_total = $total_ppn_;
        }

        $subtotal_include_ppn = $subtotal_exclude_ppn2 + $ppn_total;

        if ($subtotal_include_ppn > 100000) {
            $int_rounded = 1000;
        } else {
            $int_rounded = 100;
        }

        $price_mdr_calc = ($subtotal_include_ppn / (1 - $fee_percent) - $subtotal_include_ppn);

        // Pembulatan ke atas
        $price_mdr_exclude = ceil($price_mdr_calc / $int_rounded) * $int_rounded;
        $price_mdr_include = $price_mdr_exclude + ($price_mdr_exclude * $ppn);

        // NOTE Fee Payment Gateway
        $price_mdr_fee = $fee_nominal + ($fee_nominal * $ppn);

        // NOTE Subtotal Payment Gateway
        $subtotal_mdr_fee_final = round($price_mdr_include + $price_mdr_fee);
        $subtotal_mdr_fee_include = round($subtotal_mdr_fee_final * (1 + $ppn));
        $subtotal_mdr_fee_exclude = round($subtotal_mdr_fee_include / (1 + $ppn));
        // $subtotal_mdr_fee_exclude = $price_mdr_exclude + $fee_nominal;

        // NOTE Potong Midtrans
        $ppn_final = round(($subtotal_exclude_ppn + $subtotal_mdr_fee_exclude) * $ppn);
        $total_final = round($ppn_final + $subtotal_exclude_ppn2 + $subtotal_mdr_fee_exclude);

        $price_gateway_fee = ceil(($total_final * $fee_percent) + $fee_nominal);
        $price_service_fee = ceil($price_gateway_fee / $int_rounded) * $int_rounded;

        $result = [
            // Input
            'ppn' => $ppn,
            'sum_price' => $sum_price,
            'sum_price_ppn_only' => $sum_price_ppn_only,
            'sum_shipping' => $sum_shipping,
            'total_ppn' => $total_ppn_,

            // Calculation
            'ppn_total' => $ppn_total,
            'subtotal_exlude_ppn' => $subtotal_exclude_ppn,
            'subtotal_include_ppn' => $subtotal_include_ppn,

            'price_mdr_calc' => $price_mdr_calc,
            'price_mdr_exclude' => $price_mdr_exclude,
            'price_mdr_include' => $price_mdr_include,
            'price_mdr_fee' => $price_mdr_fee,
            'subtotal_mdr_fee_exclude' => $subtotal_mdr_fee_exclude,
            'subtotal_mdr_fee_include' => $subtotal_mdr_fee_include,
            'total_pembayaran' => $total_final,
            'price_gateway_fee' => $price_gateway_fee,

            // Biaya Layanan
            'price_service_fee' => $price_service_fee,

        ];

        return $result;
    }

}

?>
