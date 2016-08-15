<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/11 0011
 * Time: 13:35
 */
namespace app\service;

use yii;

require_once Yii::$app->basePath.'\vendor\caption\Caption.php';

class Login{

    public static function getCookie(){
        Functions::saveLog(Yii::$app->message['login']['startLogin']);
        HigoClient::getCookie();
    }

    public static function getScore(){
        $caption = new \Caption();
        return $caption->CJY_GetScore();
    }

    public static function login(){
        if(!InitService::getSystemStatus()){
            return false;
        }
        $loginTimes = 1;
        while($loginTimes <= Yii::$app->params['retryLoginTime']){
            if(HigoClient::login()){
//                Functions::saveLog(Yii::$app->message['login']['loginSuccess']);
                break;
            }else{
                Functions::saveLog(Yii::$app->message['login']['loginFailed'].','.$loginTimes.' times');
                $loginTimes++;
            }
        }
        if($loginTimes > Yii::$app->params['retryLoginTime']){
            Functions::saveLog(Yii::$app->message['login']['retryLoginFailed']);
            InitService::stopSystemStatus();
            return false;
        }
        return true;
    }

    public static function logout(){
        HigoClient::logout();
        //Functions::saveLog(Yii::$app->message['login']['logout']);
        self::clearCookie();
    }

    private static function clearCookie(){
        file_put_contents(HttpClient::getCookie(), '');
    }
}