<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 10:33
 */
namespace app\service;

use app\service\MyFunction;
use yii;
class HigoClient {

    private static $indexUrl = '';      //登录页
    private static $loginUrl = '';      //登录请求url
    private static $logoutUrl = '';     //退出url
    private static $userInfoUrl = '';   //用户信息url
    private static $captionUrl = '';    //验证码url
    private static $ballUrl = '';          //页面url
    private static $buyUrl = '';          //页面url
    private static $v = '';          //购买参数
    private static $price = [];          //购买参数

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
     * $line
     */
    public static function setUrl($line='LINE_1') {

        $ip = Functions::getAttrValue($line);
        $loginUrl = Functions::getAttrValue('URL_LOGIN');
        $userInfoUrl = Functions::getAttrValue('URL_LEFT_INFO');
        self::$loginUrl= 'http://'.$ip.$loginUrl;
        self::$userInfoUrl= 'http://'.$ip.$userInfoUrl;
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
        $arr=['account','success','true','credit'];
        if(self::stringExist($string,$arr)){//成功
            Functions::saveLog(yii::$app->message['leftInfo']['userInfoGetSuccess']);
            $userName = Functions::InterceptString($string, '{"account":"', '","credit"');    //用户名
            $edu = Functions::InterceptString($string, ',"credit":"', '","re_credit"');    //信用额度
            $yue = Functions::InterceptString($string, ',"total_amount":"', '","odds_refresh"');    //信用余额
            return false;
            //存数据库
        }else{  //失败
            Functions::saveLog(yii::$app->message['leftInfo']['userInfoGetFailed']);
            return false;
        }
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
        $arr=['integrate','success','true','changlong'];
        if(self::stringExist($string,$arr)){//成功
            Functions::saveLog(yii::$app->message['sscList']['sscListGetSuccess']);
            $json = Functions::InterceptString($string, '"integrate":', ',"changlong"');    //赔率json
            $oldNum = Functions::InterceptString($string, '"timesold":"', '","resultnum"');    //上期期数
            $oldRes = Functions::InterceptString($string, 'resultnum":', ',"status');    //上期结果
            $nowNum = Functions::InterceptString($string, 'timesnow":"', '","timeclose');    //本期期数
            self::$v = Functions::InterceptString($string, 'version_number":"', '","game_limit');    //购买参数
            //存数据库
        }else{  //失败
            Functions::saveLog(yii::$app->message['sscList']['sscListGetFailed']);
            return false;
        }
    }

    /**
     * 购买
     * @param sring购买
     */
    public static function buy($data = '') {
        if(){//判断是赔率是否购买
            Functions::saveLog(yii::$app->message['Buy']['buying']);
            $response = HttpClient::curl(self::$buyUrl, $data);
            $arr = ['suc_orders','success','true'];
            if(){

            }
        }else{
            Functions::saveLog(yii::$app->message['Buy']['noBuy']);
            return false;
        }
    }

    /**
     * 判断字符串是否有某些字符串
     */
    private static function stringExist($string,$arr=false){
        if(!$arr){
            return false;
        }
        foreach($arr as $row){
            if(!strstr($string,$row)){
                return false;
            }
        }
        return true;
    }
}