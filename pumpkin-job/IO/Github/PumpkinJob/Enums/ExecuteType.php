<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 3:58 PM
 */
namespace IO\Github\PumpkinJob\Enums;

class ExecuteType {
    /**
     * @var int 单机执行
     */
    public static int $STANDALONE = 1;
    /**
     * @var int 广播执行
     */
    public static int $BROADCAST = 2;
    /**
     * @var int MAP_REDUCE
     */
    public static int $MAP_REDUCE = 3;
    /**
     * @var int MAP
     */
    public static int $MAP = 4;
}