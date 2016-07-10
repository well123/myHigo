<?php
namespace app\service;

class MyFunction {

    /**
     * 截取字符串
     */
    public static function InterceptString($string, $s, $e) {
        $a = strpos($string, $s);
        $b = strpos($string, $e);
        return substr($string, $a + strlen($s), $b - $a - strlen($s));
    }
}