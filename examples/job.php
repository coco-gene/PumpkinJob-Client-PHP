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

$domain = getenv("DOMAIN");
$appName = getenv("APP_NAME");
$password = getenv("PASSWORD");

if(empty($domain) || empty($appName) || empty($password)) {
    echo "usage: export DOMAIN=xxx\n
            export APP_NAME=xxx\n
            export PASSWORD=xxx\n
            php job.php";
    exit;
}

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
// if there is no id param it will add a new job otherwise if will modify the job
$result = $pumpkinJobClient->saveJob($params);

$result = $pumpkinJobClient->fetchJob($jobId);

$result = $pumpkinJobClient->runJob($jobId);

// runJob will return instanceId
$instanceId = 404321016899174528;
$result = $pumpkinJobClient->fetchInstanceInfo($instanceId);

$result = $pumpkinJobClient->stopInstance($instanceId);

// see \IO\Github\PumpkinJob\Enums\InstanceStatus
$result = $pumpkinJobClient->fetchInstanceStatus($instanceId);