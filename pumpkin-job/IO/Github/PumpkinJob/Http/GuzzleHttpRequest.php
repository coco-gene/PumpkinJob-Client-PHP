<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 2:38 PM
 */
namespace IO\Github\PumpkinJob\Http;

use IO\Github\PumpkinJob\Exceptions\PumpkinJobException;
use IO\Github\PumpkinJob\Util\Logger;

class GuzzleHttpRequest extends Request {
    /**
     * @var GuzzleHttpRequest
     */
    protected static $_INSTANCE = null;
    /**
     * @var \GuzzleHttp\Client
     */
    private $_guzzleClient = null;

    public static function getInstance() {
        if(self::$_INSTANCE == null) {
            self::$_INSTANCE = new GuzzleHttpRequest();
            self::$_INSTANCE->_guzzleClient = new \GuzzleHttp\Client();
        }

        return self::$_INSTANCE;
    }

    /**
     * @param String $url
     * @param array $params array("a"=>"1", "b"=>"2")
     * @return mixed
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(String $url, array $query) {
        if(!empty($query)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= is_array($query) ? http_build_query($query) : $query;
        }
        $response = self::$_INSTANCE->_guzzleClient->request('GET', $url);
        if($response->getStatusCode() == 200) {
            return json_decode($response->getBody(), true);
        } else {
            throw new PumpkinJobException("get failed with code " . $response->getStatusCode());
        }
    }

    /**
     * @param String $url
     * @param array $params
     * @param bool $needResult
     * @return mixed
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(String $url, array $params, bool $needResult = false) {
        $options = array(
            'form_params' => $params,
        );

        $response = self::$_INSTANCE->_guzzleClient->request('POST', $url, $options);
        if($response->getStatusCode() == 200) {
            $logData = array(
                "url" => $url,
                "method" => "post",
                "params" => $params,
                "code" => $response->getStatusCode(),
                "result" => $response->getBody()
            );
            Logger::DEBUG($logData);
            return json_decode($response->getBody(), true);
        } else {
            $logData = array(
                "url" => $url,
                "method" => "post",
                "params" => $params,
                "code" => $response->getStatusCode(),
            );
            Logger::ERR($logData);
            throw new PumpkinJobException("get failed with code " . $response->getStatusCode());
        }
    }

    /**
     * @param String $url
     * @param array $params
     * @param bool $needResult
     * @return mixed
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postJson(String $url, array $params, bool $needResult = false) {
        $options = array(
            'json' => $params,
        );
        $response = self::$_INSTANCE->_guzzleClient->request('POST', $url, $options);
        if($response->getStatusCode() == 200) {
            $logData = array(
                "url" => $url,
                "method" => "postJson",
                "params" => $params,
                "code" => $response->getStatusCode(),
                "result" => $response->getBody()
            );
            Logger::DEBUG($logData);
            return json_decode($response->getBody(), true);
        } else {
            $logData = array(
                "url" => $url,
                "method" => "postJson",
                "params" => $params,
                "code" => $response->getStatusCode(),
            );
            Logger::ERR($logData);
            throw new PumpkinJobException("get failed with code " . $response->getStatusCode());
        }
    }
}