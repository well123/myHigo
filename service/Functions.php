<?php
namespace app\service;
use yii;
use app\models\Log;
use app\service\Config;

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

    /**
     * 获取config中的值
     *  $key
     */
    public static function getAttrValue($key){
        $config = Config::getInstance();
        $res = $config->find()->where(['c_name'=>$key])->asArray()->all();
        return $res['c_value'];
    }

    //获取13位时间戳
    public static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }


}