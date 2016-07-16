<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 3:12
 */
namespace app\service;

use yii;
use app\models\Log;

class HttpClient{

    private static $cookie = '';

    public static function getCookie(){
        self::$cookie = Yii::$app->basePath.'/tmp/cookie.txt';
        if(!file_exists(Yii::$app->basePath.'/tmp/cookie.txt')){
            file_put_contents(Yii::$app->basePath.'/tmp/cookie.txt', '');
        }
        return self::$cookie;
    }

    public static function curl($url, Array $data = array(), $referer = ''){
        return self::curlFetch($url, $data, $referer);
    }

    public static function downLoadCaptcha($url){
        $time = time();
        file_put_contents(Yii::$app->basePath.'/tmp/image/'.$time.'.jpg', self::curl($url));
        return Yii::$app->basePath.'/tmp/image/'.$time.'.jpg';
    }

    private static function curlFetch($url, Array $data = [], $referer = ""){
        $result = '';
        while(true){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回字符串，而非直接输出
            curl_setopt($ch, CURLOPT_HEADER, false);   // 不返回header部分
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);   // 设置socket连接超时时间
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            if(!empty($referer)){
                curl_setopt($ch, CURLOPT_REFERER, $referer);   // 设置引用网址
            }
            if(!empty(self::getCookie())){
                curl_setopt($ch, CURLOPT_COOKIEJAR, self::getCookie());   // 设置从$cookie所指文件中读取cookie信息以发送
                curl_setopt($ch, CURLOPT_COOKIEFILE, self::getCookie());   // 设置将返回的cookie保存到$cookie所指文件
            }
            if(is_array($data) && !empty($data)){
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
            set_time_limit(120); // 设置自己服务器超时时间
            $result = curl_exec($ch);
            curl_close($ch);
            if(!self::isDropped($result)){
                break;
            }
        }
        return $result;
    }

    private static function isDropped($response){
        file_put_contents('dd.txt',$response);
//        if('<html>redirect...</html>' == $response){
//            Log::getInstance();
//            return true;
//        }else{
            return false;
//        }
    }
}