<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/12
 * Time: 下午 2:56
 */

namespace App\Http\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\Log;


class HttpClientHelper
{
    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('define.cypress.domain')]);
    }

    public function sendPost($url, $params)
    {
        $header = [
            'SysToken' => config('define.cypress.token')
        ];
        try {
            $getData = $this->client->request('POST', $url, [
                'form_params' => $params,
                'headers' => $header,
                'connect_timeout' => 4
            ]);
            return $getData->getBody()->getContents();
        } catch (RequestException $e) {
            $result['status']['code'] = ErrorCode::CONNECTION_CYPRESS_ERROR;
            Log::error('Connection Cypress API broken');
            return json_encode($result);
        }
    }
}