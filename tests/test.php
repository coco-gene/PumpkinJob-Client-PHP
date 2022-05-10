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

$pumpkinJobClient = new \IO\Github\PumpkinJob\PumpkinJobClient($domain, $appName, $password);

$params = array(
    "id" => 2,
    "jobName" => "OpenAPIJob",
    "jobDescription" => "test OpenAPI",
    "jobParams" => "zdap",
    "timeExpressionType" => \IO\Github\PumpkinJob\Enums\TimeExpressionType::$API,
    "executeType" => \IO\Github\PumpkinJob\Enums\ExecuteType::$STANDALONE,
    "processorType" => \IO\Github\PumpkinJob\Enums\ProcessorType::$BUILT_IN,
    "processorInfo" => "com.yunqiic.pumpkinjob.official.processors.impl.script.ShellProcessor",
    "designatedWorkers" => "",
    "minCpuCores" => 1.1,
    "minMemorySpace" => 1.2,
    "minDiskSpace" => 1.3,

);
$result = $pumpkinJobClient->saveJob($params);

print_r($result);
