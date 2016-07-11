<?php
namespace app\service;
use yii;
use app\models\Log;

class Functions{

    /**
     * 截取字符串
     */
    public static function InterceptString($string, $s, $e){
        $a = strpos($string, $s);
        $b = strpos($string, $e);
        return substr($string, $a + strlen($s), $b - $a - strlen($s));
    }

    public static function saveLog($content){
        $log = Log::getInstance();
        $log->user_id = Yii::$app->user->id;
        $log->content = $content;
        $log->save();
    }

    public static function test(){
        print_r("dd");
    }
}