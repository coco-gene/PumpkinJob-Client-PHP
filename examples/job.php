<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 5:13 PM
 */
define("ROOT", dirname(__DIR__));
// DEBUG should create dir use command sudo mkdir /var/log/pumpkinjob && sudo chmod 777 /var/log/pumpkinjob
define("DEBUG", 1);

function autoload($clazz) {
    $file = str_replace('\\', '/', $clazz);
    if(is_file(ROOT . "/pumpkin-job/$file.php")) {
        require ROOT . "/pumpkin-job/$file.php";
    }
}

spl_autoload_register("autoload");

require ROOT . '/vendor/autoload.php';

$domain = "pumpkin-job1.7otech.com";
$appName = "pumpkinjob-agent-test";
$password = "pumpkinjob-agent-test";

use IO\Github\PumpkinJob\PumpkinJobClient;
use IO\Github\PumpkinJob\Enums\TimeExpressionType;
use IO\Github\PumpkinJob\Enums\ExecuteType;
use IO\Github\PumpkinJob\Enums\ProcessorType;

$pumpkinJobClient = new PumpkinJobClient($domain, $appName, $password);

$jobId = 2;
$params = array(
    "id" => $jobId,
    "jobName" => "OpenAPIJob",
    "jobDescription" => "test OpenAPI",
    "jobParams" => "zdap",
    "timeExpressionType" => TimeExpressionType::$TYPE[TimeExpressionType::$API],
    "executeType" => ExecuteType::$TYPE[ExecuteType::$STANDALONE],
    "processorType" => ProcessorType::$TYPE[ProcessorType::$BUILT_IN],
    "processorInfo" => "com.yunqiic.pumpkinjob.official.processors.impl.script.ShellProcessor",
    "designatedWorkers" => "",
    "minCpuCores" => 1.1,
    "minMemorySpace" => 1.2,
    "minDiskSpace" => 1.3,

);
$result = $pumpkinJobClient->saveJob($params);

$result = $pumpkinJobClient->fetchJob($jobId);

$result = $pumpkinJobClient->runJob($jobId);

$instanceId = 404321016899174528;
$result = $pumpkinJobClient->fetchInstanceInfo($instanceId);

$result = $pumpkinJobClient->stopInstance($instanceId);

$result = $pumpkinJobClient->fetchInstanceStatus($instanceId);