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
use IO\Github\PumpkinJob\Util\Logger;

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
            // {"code":0,"errno":0,"success":true,"data":1,"message":"success","msg":"success","errmsg":"success"}
            if(isset($result["success"]) && $result["success"]) {
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

        Logger::DEBUG("jassertApp:" . json_encode($result));

        return $result;
    }

    public function saveJob(array $params) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$SAVE_JOB, $this->_currentAddress);
        $params["appId"] = $this->_appId;
        $result = GuzzleHttpRequest::getInstance()->postJson($url, $params);

        Logger::DEBUG("saveJob:" . json_encode($result));

        return $result;
    }

    public function runJob(int $jobId) : array {
        return $this->_runJob($jobId, "", 0);
    }

    public function runJobDelay(int $jobId, String $instanceParams, int $delayMS) : array {
        return $this->_runJob($jobId, $instanceParams, $delayMS);
    }

    /**
     * Run a job once
     *
     * @param $jobId          int ID of the job to be run
     * @param $instanceParams string Runtime parameters of the job (TaskContext#instanceParams)
     * @param $delayMS        int Delay time（Milliseconds）
     * @return $instanceId
     */
    private function _runJob(int $jobId, String $instanceParams, int $delayMS) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$RUN_JOB, $this->_currentAddress);
        $params = array(
            "jobId" => $jobId,
            "appId" => $this->_appId,
            "delay" => $delayMS,
        );
        if (!empty($instanceParams)) {
            $params["instanceParams"] = $instanceParams;
        }
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }
}