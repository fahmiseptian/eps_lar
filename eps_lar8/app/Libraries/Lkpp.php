<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;


class Lkpp
{
    protected $ci;
    protected $config_name;
    protected $environment;
    protected $config_data;

    private $config_HOST;
    private $config_client_id;
    private $config_client_secret;

    private $table_log_report = 'api_log_report';
    private $table_log_payload = 'api_log_payload';
    private $table_log_confirm = 'api_log_confirm';
    private $table_token = 'api_lkpp_token';
    private $max_merchant_score = 5;
    private $def_is_pkp = true;

    protected $lkpp;

    function __construct()
    {

        $this->config_name = getenv('APP_CONFIG_NAME') ?? 'config_app';
        $this->environment =    env('ENVIRONMENT', 'development');

        if ($this->environment == 'development') {
            $this->lkpp = [
                'endpoint' => 'https://dev-tokodaring-api.lkpp.go.id/',
                'x_client_id' => 'u_elitexlpse2022',
                'x_client_secret' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9',
            ];
        } else {
            $this->lkpp = [
                'endpoint' => 'https://tokodaring-api.lkpp.go.id/',
                'x_client_id' => 'u_elitexlpse2022',
                'x_client_secret' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9',
            ];
        }

        $this->config_HOST          = $this->lkpp['endpoint'];
        $this->config_client_id     = $this->lkpp['x_client_id'];
        $this->config_client_secret = $this->lkpp['x_client_secret'];
    }

    public function check_token($id_member)
    {
        $check = DB::table($this->table_token)
            ->where('id_member', $id_member)
            ->first();

        return $check;
    }

    public function save_token_lpse($data, $id_member = '')
    {
        $table = $this->table_token;

        if (empty($id_member)) {
            $save = DB::table($table)->insert($data);
        } else {
            $check = $this->check_token($id_member);
            if (empty($check)) {
                $save = DB::table($table)->insert($data);
            } else {
                $save = DB::table($table)->where('id_member', $id_member)->update($data);
            }
        }

        return $save;
    }
}
