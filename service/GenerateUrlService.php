<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/15 0015
 * Time: 14:04
 */
namespace app\service;

class GenerateUrlService{

    private static $IPAddress = '';
    private static $HTTP = 'http://';

    private static function getIP(){
        if(static::$IPAddress == ''){
            static::$IPAddress = InitService::getConfig('LINE_1');
        }
        return static::$IPAddress;
    }

    public static function getUrlFrontPart(){
        return self::getIP().':'.InitService::getConfig('URL_PORT');
    }

    private static function getUrl($middlePart){
        return self::$HTTP.self::getUrlFrontPart().$middlePart;
    }

    public static function getIndexUrl(){
        return self::getUrl(InitService::getConfig('UNLOG_FRONT_PART').InitService::getConfig('URL_INDEX'));
    }

    public static function getLoginUrl(){
        return self::getUrl(InitService::getConfig('URL_LOGIN'));
    }

    public static function getLoginKeyUrl(){
        return self::getUrl(InitService::getConfig('URL_KEY'));
    }

    public static function getCaptionUrl(){
        return self::getUrl(InitService::getConfig('URL_CAPTION'));
    }

    public static function getLogoutUrl(){
        return self::getUrl(InitService::getConfig('LOGGED_FRONT_PART').InitService::getConfig('URL_LOGOUT'));
    }

    public static function getUserLeftInfo(){
        return self::getUrl(InitService::getConfig('LOGGED_FRONT_PART').InitService::getConfig('URL_LEFT_INFO')).'/?&_='.Functions::getMillisecond().'__ajax';
    }

    public static function getOrderList(){
        return self::getUrl(InitService::getConfig('LOGGED_FRONT_PART').InitService::getConfig('URL_ORDER_LIST')).'/?&_='.Functions::getMillisecond().
        '__autorefresh';
    }

    public static function getBuyUrl(){
        return self::getUrl(InitService::getConfig('LOGGED_FRONT_PART').InitService::getConfig('URL_BUY')).Functions::getMillisecond().'__ajax';
    }
    public static function getLoginReferUrl(){
        return self::getUrl(InitService::getConfig('URL_LOGOUT'));
    }
}