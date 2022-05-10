<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 3:56 PM
 */
namespace IO\Github\PumpkinJob\Enums;

class TimeExpressionType {
    public static int $API = 1;
    public static int $CRON = 2;
    public static int $FIXED_RATE = 3;
    public static int $FIXED_DELAY = 4;
    public static int $WORKFLOW = 5;

    public static array $TYPE = array(
        1 => "API",
        2 => "CRON",
        3 => "FIXED_RATE",
        4 => "FIXED_DELAY",
        5 => "WORKFLOW",
    );
}