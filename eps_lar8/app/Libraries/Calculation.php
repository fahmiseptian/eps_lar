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
}

?>