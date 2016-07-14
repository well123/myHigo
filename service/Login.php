<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/11 0011
 * Time: 13:35
 */
namespace app\service;

use yii;
use app\models\User;

require_once yii::$app->basePath.'\vendor\caption\Caption.php';

class Login{

    public static function getCookie(){
        Functions::saveLog(yii::$app->message['startLogin']);
        HigoClient::getCookie();
    }

    public static function getScore(){
        $caption = new \Caption();
        return $caption->CJY_GetScore();
    }


    public static function login(){
        $loginTimes = 1;
        while($loginTimes <= yii::$app->params['retryLoginTime']){
            if(HigoClient::login()){
                Functions::saveLog(yii::$app->message['loginSuccess']);
                break;
            }else{
                Functions::saveLog(yii::$app->message['loginFailed'].','.$loginTimes.' times');
                $loginTimes++;
            }
        }
        $loginTimes > yii::$app->params['retryLoginTime'] && Functions::saveLog(yii::$app->message['retryLoginFailed']);
    }

    public static function logout(){
        Functions::saveLog(yii::$app->message['logout']);
        HigoClient::logout();
        self::clearCookie();
    }

    private static function clearCookie(){
        file_put_contents(HttpClient::getCookie(), '');
    }
}