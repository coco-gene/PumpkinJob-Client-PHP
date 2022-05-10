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

