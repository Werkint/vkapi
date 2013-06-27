<?php
namespace Vkapi;

class Vkapi
{
    const API_URL = 'http://api.vk.com/api.php';

    protected $api_secret;
    protected $app_id;

    public function __construct(
        $app_id,
        $api_secret
    ) {
        $this->app_id = $app_id;
        $this->api_secret = $api_secret;
    }

    protected function getSignature(array $params)
    {
        $sig = '';
        foreach ($params as $k => $v) {
            $sig .= $k . '=' . $v;
        }
        $sig .= $this->api_secret;
        return md5($sig);
    }

    protected function query(array $params)
    {
        $query = static::API_URL . '?' . http_build_query($params);
        $res = file_get_contents($query);
        return json_decode($res, true);
    }

    protected function populateParams(array &$params)
    {
        $params['api_id'] = $this->app_id;
        $params['v'] = '3.0';
        $params['timestamp'] = time();
        $params['format'] = 'json';
        mt_srand(microtime(true));
        $params['random'] = mt_rand(10000, 90000);
        ksort($params);
        $params['sig'] = $this->getSignature($params);
    }

    public function api($method, array $params = [])
    {
        $params['method'] = $method;
        $this->populateParams($params);

        return $this->query($params);
    }
}


