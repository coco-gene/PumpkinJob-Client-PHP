<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 2:55 PM
 */
namespace IO\Github\PumpkinJob\Http;

use IO\Github\PumpkinJob\Exceptions\PumpkinJobException;
use IO\Github\PumpkinJob\Util\Logger;

class HttpRequest extends Request {
    const USER_AGENT = 'pumpkinjob-php/1.0';

    public static function get($url, $parameters, array $options = array()) {
        return self::request("GET", $url, $parameters, $options);
    }

    public static function post($url, $parameters, array $options = array()) {
        return self::request("POST", $url, $parameters, $options);
    }

    /**
     *
     * @param string $method
     * @param string $url
     * @param mixed  $parameters
     * @param array  $options
     *
     * @return string|boolean
     * */
    public static function request($method, $url, $parameters, array $options = array()) {
        $startTime = microtime(true);
        $requestId = g($_SERVER, 'HTTP_X_UNIQUE_ID', g($_SERVER, 'UNIQUE_ID', 0));
        $deviceId = g($_SERVER, 'X_DEVICE_ID', '');

        $sPos = strpos($url, '//');
        $sStr = substr($url, $sPos + 2);
        $ePos = strpos($sStr, '/');
        $apiName = substr($sStr, 0, $ePos);
        $requestPath = substr($sStr, $ePos);
        $userId = g($_SERVER, 'user_id', g($_SERVER, 'X_USER_ID', 0));
        $deviceId = g($_SERVER, 'X_DEVICE_ID', 0);

        $headers = isset($options['headers']) ? $options['headers'] : array();
        $asproxy = isset($options['asproxy']) ? $options['asproxy'] : '';

        $headers = array_merge(array(
            "USER-AGENT" => 'pumpkinjob-php/1.0',
            'X_UNIQUE_ID' => $requestId,
            'X_USER_ID' => $userId,
            'X_DEVICE_ID' => $deviceId
        ), $headers);

        if (!class_exists("http\Client\Request")) {
            $options = array_merge(array(
                'timeout' => 10,
                'user_agent' => self::USER_AGENT,
            ), $options);
            $result = CurlManager::getInstance($options)->request($method, $url, $parameters, $headers, $options['timeout']);
            //记录调用日志
            Logger::DEBUG($apiName, "http", $requestPath, $method,
                $parameters, $result, $deviceId, $startTime);
            return $result;
        }

        $request = new \http\Client\Request($method, $url, $headers);
        $options = array_merge(array(
            'connecttimeout' => 10,
            'timeout' => 10,
            'redirect' => 3,
            'retrycount' => 3,
            'retrydelay' => 0.1,
            'useragent' => self::USER_AGENT,
        ), $options);
        $request->setOptions($options);
        if ($method == 'GET') {
            $request->addQuery($parameters);
        } else {
            $body = new \http\Message\Body();
            //$request->setBody($body->addForm($parameters));
            if (is_array($parameters) || is_object($parameters)) {
                $parameters = http_build_query($parameters);
            }
            $body->append($parameters);
            $request->setBody($body);
            $request->setContentType("application/x-www-form-urlencoded");
        }

        try {
            $client = new \http\Client;
            $client->enqueue($request)->send();

            $response = $client->getResponse($request);

            // just make the request as proxy
            if ($asproxy) {
                $version = $response->getHttpVersion();
                $code = $response->getResponseCode();
                $status = $response->getResponseStatus();
                header(sprintf("HTTP/%s %s %s", $version, $code, $status));
                echo $response->getBody()->toString();
                exit;
            }

            if ($response && $response->getResponseCode() == 200) {
                $result = $response->getBody()->toString();
                //记录调用日志
                Logger::DEBUG($apiName, "http", $requestPath, $method,
                    $parameters, $result, $deviceId, $startTime);
                return $result;
            }
            $result = array(
                'code' => $response->getResponseCode(),
                'status' => $response->getResponseStatus(),
                'body' => $response->getBody()->toString(),
                'url' => $url,
                'parameters' => $parameters,
            );
            throw (new PumpkinJobException("$method $url failed", $result['code']))->setResult($result);
        } catch (\http\Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'status' => $e->getMessage(),
                'body' => '',
            );
            //记录调用日志
            Logger::DEBUG($apiName, "http", $requestPath, $method,
                $parameters, $result, $deviceId, $startTime);
            throw (new PumpkinJobException("$method $url failed :" . $e->getMessage(), $e->getCode()))->setResult($result);
        }
    }
}