<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

class Lpse
{

    function insertLogPayload($payload)
    {
        $id = DB::table('api_log_payload')
            ->insertGetId($payload);

        return $id;
    }

    public function getIdMember($data_member)
    {
        $id_instansi_lpse = $data_member['id_instansi_lpse'];
        $id_satker_lpse = $data_member['id_satker_lpse'];
        $id_bidang_lpse = $data_member['id_bidang_lpse'];

        $nm_instansi = $data_member['instansi'];
        $nm_satker = $data_member['satker'];
        $nm_bidang = $data_member['bidang'];

        $member = DB::table('member')
            ->where('email', $data_member['email'])
            ->where('id_instansi_lpse', $id_instansi_lpse)
            ->where('id_satker_lpse', $id_satker_lpse)
            ->where('id_bidang_lpse', $id_bidang_lpse)
            ->first();

        $id_instansi = $this->check_instansi($id_instansi_lpse, $nm_instansi);
        $id_satker = $this->check_satker($id_satker_lpse, $nm_satker, $id_instansi);
        $id_bidang = $this->check_bidang($id_bidang_lpse, $nm_bidang, $id_satker_lpse);

        if ($member) {
            $id = $member->id;

            if ($id_instansi) {
                DB::table('member')->where('id', $id)->update(['id_instansi' => $id_instansi]);
            }

            if ($id_satker) {
                DB::table('member')->where('id', $id)->update(['id_satker' => $id_satker]);
            }

            if ($id_bidang) {
                DB::table('member')->where('id', $id)->update(['id_bidang' => $id_bidang]);
            }

            DB::table('member')->where('id', $id)->update($data_member);
        } else {
            $data_member['id_member_type'] = 3;
            $data_member['password'] = 'lpse_password';
            $data_member['activation_key'] = 'lpse_activation_key';
            $data_member['member_status'] = 'active';

            $id = DB::table('member')->insertGetId($data_member);
        }

        return $id;
    }

    public function check_instansi($id_instansi, $nama_instansi)
    {
        $table = 'm_lpse_instansi';

        $row = DB::table($table)
            ->where('id_instansi', $id_instansi)
            ->first();

        if ($row) {
            // Data Exists
            $dataSave = [
                'nama' => $nama_instansi,
                'updated_date' => now(),
            ];

            DB::table($table)->where('id_instansi', $id_instansi)->update($dataSave);
            $id = $row->id;
        } else {
            // Data not Exists
            $dataSave = [
                'id_instansi' => $id_instansi,
                'nama' => $nama_instansi,
                'created_date' => now(),
            ];

            $id = DB::table($table)->insertGetId($dataSave);
        }

        return $id;
    }

    public function check_satker($id_satker, $nama_satker, $id_instansi)
    {
        $table = 'm_lpse_satker';

        $row = DB::table($table)
            ->where('id_satker', $id_satker)
            ->first();

        if ($row) {
            // Data Exists
            $dataSave = [
                'id_instansi' => $id_instansi,
                'nama' => $nama_satker,
                'updated_date' => now(),
            ];

            DB::table($table)->where('id_satker', $id_satker)->update($dataSave);
            $id = $row->id;
        } else {
            // Data not Exists
            $dataSave = [
                'id_satker' => $id_satker,
                'id_instansi' => $id_instansi,
                'nama' => $nama_satker,
                'created_date' => now(),
            ];

            $id = DB::table($table)->insertGetId($dataSave);
        }

        return $id;
    }

    public function check_bidang($id_bidang, $nama_bidang, $id_satker)
    {
        $table = 'm_lpse_bidang';

        $row = DB::table($table)
            ->where('id_bidang', $id_bidang)
            ->first();

        // Get id satker
        $id_satker_ = $this->get_satker_byId($id_satker) ?? null;

        if ($row) {
            // Data Exists
            $dataSave = [
                'nama' => $nama_bidang,
                'id_satker' => $id_satker_,
                'updated_date' => now(),
            ];

            DB::table($table)->where('id_bidang', $id_bidang)->update($dataSave);
            $id = $row->id;
        } else {
            // Data not Exists
            $dataSave = [
                'id_bidang' => $id_bidang,
                'id_satker' => $id_satker_,
                'nama' => $nama_bidang,
                'created_date' => now(),
            ];

            $id = DB::table($table)->insertGetId($dataSave);
        }

        return $id;
    }

    public function get_satker_byId($id_satker)
    {
        // NOTE $id_satker from LPSE
        $data = DB::table('m_lpse_satker')
            ->select('id')
            ->where('id_satker', $id_satker)
            ->first();

        if ($data) {
            return $data->id;
        }

        return false;
    }

    public function getDataById($id)
    {
        $result = DB::table('member')
            ->select('id', 'nama', 'foto', 'no_hp', 'password', 'email', 'member_status', 'id_member_type')
            ->where('id', $id)
            ->get();

        return $result;
    }

    public function checkUserAPI($data_user)
    {
        $count = DB::table('api_user')
            ->where($data_user)
            ->where('active_status', 'Y')
            ->count();

        return $count;
    }

    public function updateLogPayload($data_update, $id)
    {
        DB::table('api_log_payload')
            ->where('id', $id)
            ->update($data_update);

        return true;
    }

    public function check_token($token = null)
    {
        // Cek mode pemeliharaan
        $mainten = DB::table('site_config')->value('maintenance_mode');

        // Ambil data berdasarkan token
        $data = DB::table('api_log_payload as alp')
            ->select(
                'm.id',
                'alp.id_member',
                'pc.lpse_report_id AS id_lpse_cat',
                'alp.category',
                'alp.token_eps',
                'alp.token_lpse',
                'm.email',
                'm.username',
                'm.nama',
                'm.jenis_kelamin',
                'm.no_hp',
                'm.tgl_lahir',
                'm.foto',
                'm.npwp',
                'm.npwp_address',
                'm.instansi',
                'm.satker',
                'm.bidang',
                'm.id_instansi_lpse',
                'm.id_satker_lpse',
                'm.id_bidang_lpse',
                'm.id_instansi',
                'm.id_satker',
                'm.id_bidang',
                'm.id_member_type',
                'm.member_status',
                'm.is_email_subscribe',
                'm.activation_key',
                'm.registered_member',
                'm.last_update'
            )
            ->leftJoin('member as m', 'm.id', '=', 'alp.id_member')
            ->leftJoin('product_category as pc', 'pc.lpse_code', '=', 'alp.category')
            ->where('token_eps', $token)
            ->where('expired_dt_token', '>', now())
            ->first();

        if ($mainten == '1') {
            echo '<img src="https://eliteproxy.co.id/assets/images/maintenance.gif" width="100%">';
            exit();
        } elseif ($data) {
            return $data;
        } else {
            echo '<img src="https://eliteproxy.co.id/assets/images/default_403.gif" width="100%"></center>';
            exit();
        }
    }
}
