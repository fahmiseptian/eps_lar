<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class Lkpp {
    <?php

/**
 *
 * Created by Mochammad Faisal
 * Created at 2023-07-25 16:23:32
 * Updated at
 *
 */

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

    function __construct()
    {

        // $this->config_name = getenv('APP_CONFIG_NAME') ?? 'config_app';
        // $this->environment = getenv('ENVIRONMENT') ?? 'development' ?: 'development';
        $this->config_data = $this->get_config();
    }

    private function get_config()
    {
        // $get_data = $this->ci->salz->get_config($this->config_name, $this->environment);

        if ($get_data) {
            $config_data = $get_data['external']['lkpp'];

            $endpoint = $config_data['endpoint'];
            $client_id = $config_data['x_client_id'];
            $client_secret = $config_data['x_client_secret'];

            $r = [
                'endpoint' => $endpoint,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
            ];

            $this->config_HOST = $endpoint;
            $this->config_client_id = $client_id;
            $this->config_client_secret = $client_secret;
        } else {
            $r = false;
        }

        return $r;
    }
}
