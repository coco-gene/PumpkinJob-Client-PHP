<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/9
 * Time: 5:23 PM
 */
namespace IO\Github\PumpkinJob;

use IO\Github\PumpkinJob\Exceptions\PumpkinJobException;
use IO\Github\PumpkinJob\Http\GuzzleHttpRequest;

class PumpkinJobClient {

    private $_allAddress = null;
    private $_appId = null;
    private $_currentAddress = null;

    /**
     * @param String $domain
     * @param String $appName
     * @param String $password
     * @throws PumpkinJobException
     */
    public function __construct(String $domain, String $appName, String $password) {
        $addressList = array($domain);
        $this->pumpkinJobClient($addressList, $appName, $password);
    }

    /**
     * @param array $addressList
     * @param String $appName
     * @param String $password
     * @return void
     * @throws PumpkinJobException
     */
    public function pumpkinJobClient(array $addressList, String $appName, String $password) {
        if(empty($addressList)) {
            throw new PumpkinJobException("addressList can't be null!");
        }
        if(empty($appName)) {
            throw new PumpkinJobException("appName can't be null");
        }

        $this->_allAddress = $addressList;
        foreach ($addressList as $key => $value) {
            $url = PumpkinJobClient::getUrl(OpenAPIConstant::$ASSERT, $value);
            $result = $this->assertApp($appName, $password, $url);
            if($result["success"]) {
                $this->_appId = $result["data"];
                $this->_currentAddress = $value;
                break;
            }
        }
        if(empty($this->_appId)) {
            throw new PumpkinJobException("can get data");
        }
    }

    private static function getUrl(String $path, String $address) : String {
        return "http://" . $address . OpenAPIConstant::$WEB_PATH . $path;
    }

    private static function assertApp(String $appName, String $password, String $url) {
        // post
        $params = array(
            "appName" => $appName,
            "password" => $password,
        );
        /**
         * Array
        (
        [code] => 0
        [errno] => 0
        [success] => 1
        [data] => 1
        [message] => success
        [msg] => success
        [errmsg] => success
        )
         */
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }
}