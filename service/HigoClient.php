<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 10:33
 */
namespace app\service;

use app\service\MyFunction;

class HigoClient {

    private static $indexUrl = '';
    private static $loginUrl = '';
    private static $logoutUrl = '';
    private static $userInfoUrl = '';
    private static $captionUrl = '';

    public static function getCookie(){
        HttpClient::curl(self::$indexUrl);
    }

    public static function getCaption(){
        return HttpClient::downLoadCaptcha(self::$captionUrl);
    }

    public static function login($caption) {
        /**
         * $userInfo = array('name'=>'','password'=>'','caption'=>'');
         * 需要你来写获取用户登录名和密码的方法
         */
        $userInfo = User::getUserInfo();
        $response = HttpClient::curl(self::$loginUrl, $userInfo);
        return self::isLoginSuccess($response);
    }

    public static function logout(){
        $response = HttpClient::curl(self::$logoutUrl);
        return self::isLoginSuccess($response);
    }
    private static function isLoginSuccess(Array $array) {
        return $array['code'] == 0;
    }

    /**
     * 拼接url
     * @param $ip string  ip地址
     * @param $type string url类型
     */
    public static function setUrl($ip, $type) {

    }

    /**
     * 左侧信息请求
     */
    public static function leftInfo() {
        $response = HttpClient::curl(self::$loginUrl);
        return self::isGetLeftInfoSuccess($response);
    }

    /**
     * 获取左侧数据
     * @param $string
     * @return bool
     */
    private static function isGetLeftInfoSuccess($string) {
        $userName = MyFunction::InterceptString($string, '{"account":"', '","credit"');    //用户名
        $edu = MyFunction::InterceptString($string, ',"credit":"', '","re_credit"');    //信用额度
        $yue = MyFunction::InterceptString($string, ',"total_amount":"', '","odds_refresh"');    //信用余额
    }

    /**
     * 时时彩页面信息请求
     * @param $data array post数据
     * @return bool
     */
    public static function sscInfo($data = array()) {
        if (empty($data)) {
            $data = array('action' => 'ajax');
        }
        $response = HttpClient::curl(self::$loginUrl, $data);
        return self::isGetSscInfo($response);
    }

    /**
     * 获取上期开奖结果，本期，赔率
     * @param $string
     */
    private static function isGetSscInfo($string) {
        $old = MyFunction::InterceptString($string);
    }

    /**
     * 购买
     * @param sring购买
     */
    public static function buy($data = '') {

    }
}