<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 2:02 PM
 */
namespace IO\Github\PumpkinJob\Util;

class CommonUtils {

    /**
     * @param $arr
     * @param $key
     * @param $default
     * @return mixed|string
     */
    public static function isEmpty($arr, $key, $default = '') {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }
}