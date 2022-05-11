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
     * Init PumpkinJobClient with domain, appName and password.
     *
     * @param String $domain
     * @param String $appName
     * @param String $password
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(String $domain, String $appName, String $password) {
        $addressList = array($domain);
        $this->pumpkinJobClient($addressList, $appName, $password);
    }

    /**
     * Init PumpkinJobClient with server address, appName and password.
     *
     * @param array $addressList
     * @param String $appName
     * @param String $password
     * @return void
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
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

    /**
     * @param String $appName
     * @param String $password
     * @param String $url
     * @return mixed
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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
        $result = GuzzleHttpRequest::getInstance()->post($url, $params, true);

        Logger::DEBUG("jassertApp:" . json_encode($result));

        return $result;
    }

    /**
     * Save one Job
     * When an ID exists in $params, it is an update operation. Otherwise, it is a crate operation.
     *
     * @param array $params
     * @return int jobId
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveJob(array $params) : int {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$SAVE_JOB, $this->_currentAddress);
        $params["appId"] = $this->_appId;
        $result = GuzzleHttpRequest::getInstance()->postJson($url, $params);

        Logger::DEBUG("saveJob:" . json_encode($result));

        return $result;
    }

    /**
     * Copy one Job
     *
     * @param int $jobId Job id
     * @return int Id of job copy
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function copyJob(int $jobId) : int{
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$COPY_JOB, $this->_currentAddress);
        $params = array(
            "jobId" => $jobId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    /**
     * Query JobInfo by jobId
     *
     * @param int $jobId jobId
     * @return array Job meta info
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchJob(int $jobId) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$FETCH_JOB, $this->_currentAddress);
        $params = array(
            "jobId" => $jobId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    /**
     * Query all JobInfo
     *
     * @return array All JobInfo
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchAllJob() : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$FETCH_ALL_JOB, $this->_currentAddress);
        $params = array(
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    /**
     * Disable one Job by jobId
     *
     * @param int $jobId
     * @return mixed return object
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function disableJob(int $jobId) {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$DISABLE_JOB, $this->_currentAddress);
        $params = array(
            "jobId" => $jobId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params, true);

        return $result["success"];
    }

    /**
     * Enable one job by jobId
     *
     * @param int $jobId
     * @return mixed return object
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function enableJob(int $jobId) {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$ENABLE_JOB, $this->_currentAddress);
        $params = array(
            "jobId" => $jobId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params, true);

        return $result["success"];
    }

    /**
     * Delete one job by jobId
     *
     * @param int $jobId
     * @return mixed return object
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteJob(int $jobId) {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$DELETE_JOB, $this->_currentAddress);
        $params = array(
            "jobId" => $jobId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params, true);

        return $result["success"];
    }

    /**
     * Run a job once
     *
     * @param int $jobId ID of the job to be run
     * @return int instanceId
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function runJob(int $jobId) : int {
        return $this->_runJob($jobId, "", 0);
    }

    /**
     * Run a job once
     *
     * @param int $jobId ID of the job to be run
     * @param String $instanceParams Runtime parameters of the job (TaskContext#instanceParams)
     * @param int $delayMS Delay time（Milliseconds）
     * @return int instanceId
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function runJobDelay(int $jobId, String $instanceParams, int $delayMS) : int {
        return $this->_runJob($jobId, $instanceParams, $delayMS);
    }

    public function fetchInstanceInfo(int $instanceId) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$FETCH_INSTANCE_INFO, $this->_currentAddress);
        $params = array(
            "instanceId" => $instanceId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    public function stopInstance(int $instanceId) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$STOP_INSTANCE, $this->_currentAddress);
        $params = array(
            "instanceId" => $instanceId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    /**
     * Query status about a job instance
     *
     * @param $instanceId int
     * @return $result int
     */
    public function fetchInstanceStatus($instanceId) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$FETCH_INSTANCE_STATUS, $this->_currentAddress);
        $params = array(
            "instanceId" => $instanceId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    /**
     * Retry failed job instance
     * Notice: Only job instance with completion status (success, failure, manually stopped, cancelled) can be retried, and retries of job instances within workflows are not supported yet.
     *
     * @param $instanceId int
     * @return $result return object
     */
    public function retryInstance($instanceId) : array {
        $url = PumpkinJobClient::getUrl(OpenAPIConstant::$RETRY_INSTANCE, $this->_currentAddress);
        $params = array(
            "instanceId" => $instanceId,
            "appId" => $this->_appId,
        );
        $result = GuzzleHttpRequest::getInstance()->post($url, $params);

        return $result;
    }

    /**
     * Run a job once
     *
     * @param int $jobId ID of the job to be run
     * @param String $instanceParams Runtime parameters of the job (TaskContext#instanceParams)
     * @param int $delayMS Delay time（Milliseconds）
     * @return int instanceId
     * @throws Exceptions\HttpRequestException
     * @throws PumpkinJobException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function _runJob(int $jobId, String $instanceParams, int $delayMS) : int {
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