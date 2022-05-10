<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 10:40 AM
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

$params = array(
    "id" => 2,
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
// {"id":2,"jobName":"OpenAPIJob","jobDescription":"test OpenAPI","appId":1,"jobParams":"zdap","timeExpressionType":"CRON","timeExpression":null,"executeType":"BROADCAST","processorType":"EXTERNAL","processorInfo":"com.yunqiic.pumpkinjob.official.processors.impl.script.ShellProcessor","maxInstanceNum":0,"concurrency":5,"instanceTimeLimit":0,"instanceRetryNum":0,"taskRetryNum":0,"minCpuCores":1.1,"minMemorySpace":1.2,"minDiskSpace":1.3,"enable":true,"designatedWorkers":"","maxWorkerCount":0,"notifyUserIds":null,"extra":null,"dispatchStrategy":"HEALTH_FIRST","lifecycle":null}
// {"id":2,"jobName":"OpenAPIJob","jobDescription":"test OpenAPI","appId":1,"jobParams":"#!/bin/bash\nsu - genome <<EOF\nid;\nzdap;\nEOF","timeExpressionType":"API","timeExpression":null,"executeType":"STANDALONE","processorType":"BUILT_IN","processorInfo":"com.yunqiic.pumpkinjob.official.processors.impl.script.ShellProcessor","maxInstanceNum":0,"concurrency":5,"instanceTimeLimit":0,"instanceRetryNum":0,"taskRetryNum":0,"minCpuCores":1.1,"minMemorySpace":1.2,"minDiskSpace":1.3,"enable":true,"designatedWorkers":"","maxWorkerCount":0,"notifyUserIds":null,"extra":null,"dispatchStrategy":"HEALTH_FIRST","lifecycle":null}
$result = $pumpkinJobClient->saveJob($params);

// {"code":0,"errno":0,"success":true,"data":2,"message":"success","msg":"success","errmsg":"success"}
print_r($result);
