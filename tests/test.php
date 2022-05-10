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
// {"id":2,"jobName":"OpenAPIJob","jobDescription":"test OpenAPI","appId":1,"jobParams":"zdap","timeExpressionType":"CRON","timeExpression":null,"executeType":"BROADCAST","processorType":"EXTERNAL","processorInfo":"com.yunqiic.pumpkinjob.official.processors.impl.script.ShellProcessor","maxInstanceNum":0,"concurrency":5,"instanceTimeLimit":0,"instanceRetryNum":0,"taskRetryNum":0,"minCpuCores":1.1,"minMemorySpace":1.2,"minDiskSpace":1.3,"enable":true,"designatedWorkers":"","maxWorkerCount":0,"notifyUserIds":null,"extra":null,"dispatchStrategy":"HEALTH_FIRST","lifecycle":null}
// {"id":2,"jobName":"OpenAPIJob","jobDescription":"test OpenAPI","appId":1,"jobParams":"#!/bin/bash\nsu - genome <<EOF\nid;\nzdap;\nEOF","timeExpressionType":"API","timeExpression":null,"executeType":"STANDALONE","processorType":"BUILT_IN","processorInfo":"com.yunqiic.pumpkinjob.official.processors.impl.script.ShellProcessor","maxInstanceNum":0,"concurrency":5,"instanceTimeLimit":0,"instanceRetryNum":0,"taskRetryNum":0,"minCpuCores":1.1,"minMemorySpace":1.2,"minDiskSpace":1.3,"enable":true,"designatedWorkers":"","maxWorkerCount":0,"notifyUserIds":null,"extra":null,"dispatchStrategy":"HEALTH_FIRST","lifecycle":null}
$result = $pumpkinJobClient->saveJob($params);

// {"code":0,"errno":0,"success":true,"data":2,"message":"success","msg":"success","errmsg":"success"}
print_r($result);

/**
 * Array
(
[0] => tests/test.php
)
 */
print_r($argv);
if(count($argv) > 1 && $argv[1] == "runJob") {
    /**
     * Array
    (
    [code] => 0
    [errno] => 0
    [success] => 1
    [data] => 404321016899174528
    [message] => success
    [msg] => success
    [errmsg] => success
    )
     */
    $result = $pumpkinJobClient->runJob($jobId);
    print_r($result);

    /**
     * Array
    (
    [code] => 0
    [errno] => 0
    [success] => 1
    [data] => 404326845819912320
    [message] => success
    [msg] => success
    [errmsg] => success
    )
     */
    $result = $pumpkinJobClient->runJobDelay($jobId, "this is instanceParams", 60000);
    print_r($result);
}

// 404321016899174528
$result = $pumpkinJobClient->fetchInstanceInfo(404321016899174528);
/**
 * Array
(
[code] => 0
[errno] => 0
[success] => 1
[data] => Array
(
[jobId] => 2
[appId] => 1
[instanceId] => 404321016899174528
[wfInstanceId] =>
[jobParams] => zdap
[instanceParams] =>
[status] => 5
[type] => 1
[result] => [INPUT]: usage: zdap [-h] [--version]            {bam2bdg,bamsort,bamqc,bwamem,contam,fastqc,staralign,starsrna,htseqcount,cutadap,markdup,bqsr,mutect2,hapcaller,mergetable,deseq2,riboqual,ribopred,macs2callpeak,mergefq}            ...zdap -- Data processing toolpositional arguments:  {bam2bdg,bamsort,bamqc,bwamem,contam,fastqc,staralign,starsrna,htseqcount,cutadap,markdup,bqsr,mutect2,hapcaller,mergetable,deseq2,riboqual,ribopred,macs2callpeak,mergefq}    bam2bdg             Convert bam to bedgraph    bamsort             Sort bam file    bamqc               Quality control for bam alignment    bwamem              BWA MEM genome align    contam              Estimation of contamination using VerifyBamID    fastqc              FastQC quality control    staralign           STAR RNASeq genome align    starsrna            STAR small RNA genome align    htseqcount          HTSeq count gene expression    cutadap             Cut adapter sequences    markdup             GATK MarkDuplicates    bqsr                GATK ...
[expectedTriggerTime] => 1652173642347
[actualTriggerTime] => 1652173642374
[finishedTime] => 1652173645407
[taskTrackerAddress] => 172.17.0.2:27777
[runningTimes] => 1
[gmtCreate] => 2022-05-10T09:07:22.350+00:00
[gmtModified] => 2022-05-10T09:07:25.407+00:00
)

[message] => success
[msg] => success
[errmsg] => success
)
 */
print_r($result);

$result = $pumpkinJobClient->stopInstance(404321016899174528);
print_r($result);

$result = $pumpkinJobClient->fetchInstanceStatus(404321016899174528);
/**
 * Array
(
[code] => 0
[errno] => 0
[success] => 1
[data] => 5
[message] => success
[msg] => success
[errmsg] => success
)
 */
print_r($result);
