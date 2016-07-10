<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 10:33
 */
namespace app\service;

class HigoClient{

    private static $loginUrl = '';
    private static $logoutUrl = '';
    private static $userInfoUrl = '';

    public static function login(){
        /**
         * $userInfo = array('name'=>'','password'=>'','caption'=>'');
         * 需要你来写获取用户登录名和密码的方法
         */
        $userInfo = User::getUserInfo();
        $response = HttpClient::curl(self::$loginUrl, $userInfo);
        return self::isLoginSuccess($response);
    }

    private static function isLoginSuccess(Array $array){
        return $array['code'] == 0;
    }
}