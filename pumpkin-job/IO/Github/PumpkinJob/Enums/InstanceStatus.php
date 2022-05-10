<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 5:48 PM
 */
namespace IO\Github\PumpkinJob\Enums;

class InstanceStatus {
    /**
     * @var int 等待派发
     */
    public static int $WAITING_DISPATCH = 1;
    /**
     * @var int 等待Worker接收
     */
    public static int $WAITING_WORKER_RECEIVE = 2;
    /**
     * @var int 运行中
     */
    public static int $RUNNING = 3;
    /**
     * @var int 失败
     */
    public static int $FAILED = 4;
    /**
     * @var int 成功
     */
    public static int $SUCCEED = 5;

    /**
     * @var int 取消
     */
    public static int $CANCELED = 9;

    /**
     * @var int 手动停止
     */
    public static int $STOPPED = 10;
}