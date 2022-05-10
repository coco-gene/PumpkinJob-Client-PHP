<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 4:03 PM
 */
namespace IO\Github\PumpkinJob\Enums;

class ProcessorType {
    /**
     * @var int 内建处理器
     */
    public static int $BUILT_IN = 1;
    /**
     * @var int 外部处理器（动态加载）
     */
    public static int $EXTERNAL = 4;
}