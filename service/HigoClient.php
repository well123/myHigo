<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 10:33
 */
namespace app\service;

use yii;
use app\models\Record;
class HigoClient{

    private static $userInfoUrl = '';
    private static $leftInfoUrl = 'http://1.255.41.227:8213/scpqa47904f_3625/ssc/order/list';
    private static $ballUrl = '';          //页面url
    private static $buyUrl = '';          //页面url
    private static $v = '';          //购买参数
    private static $price = [];          //购买参数
    private static $verifyValue = '';
    /** 存数据库数据 **/
    private static $one = '';
    private static $two = '';
    private static $tree = '';
    private static $four = '';
    private static $five = '';
    private static $json = '';
    private static $oldNum = '';    //上期期数
    private static $oldRes = '';    //上期结果
    private static $nowNum = '';    //本期期数是
    /** 用户信息 **/
    private static $userName = '';
    private static $edu = '';
    private static $yue = '';

    public static function getCookie(){
        HttpClient::curl(GenerateUrlService::getIndexUrl());
    }

    public static function getCaption(){
        $randomStr = mt_rand() / mt_getrandmax();
        $verifyParams = [
            'systemversion' => InitService::getConfig('systemversion'),
            'u' => $randomStr
        ];
        $captionParams = [
            'systemversion' => InitService::getConfig('systemversion'),
            't' => self::$verifyValue[0]
        ];
        self::$verifyValue =
            explode('_', HttpClient::curl(GenerateUrlService::getLoginKeyUrl().http_build_query($verifyParams)));
        return HttpClient::downLoadCaptcha(GenerateUrlService::getCaptionUrl().http_build_query($captionParams));
    }

    public static function getIndexPageContent(){
        return HttpClient::curl(GenerateUrlService::getIndexUrl());
    }

    public static function getCaptionStr(){
        $result = [
            'error' => 1,
            'caption' => 0,
        ];
        while($result['error'] != 0){
            $captionImg = HigoClient::getCaption();
            $caption = new \Caption();
            $result = $caption->CJY_RecognizeBytes($captionImg);
        }
        return $result['caption'];
    }

    public static function login(){
        $caption = self::getCaptionStr();
        $params = [
            'VerifyCode' => $caption,
            '__VerifyValue' => self::$verifyValue[1],
            '__name' => InitService::getConfig('USERNAME'),
            'password' => InitService::getConfig('PASSWORD'),
            'isSec' => InitService::getConfig('isSec'),
            'cid' => InitService::getConfig('cid'),
            'cname' => InitService::getConfig('cname'),
            'systemversion' => InitService::getConfig('systemversion')
        ];
        $response = HttpClient::curl(GenerateUrlService::getLoginUrl(), $params, GenerateUrlService::getIndexUrl());
        var_dump($response);
        exit;
        return self::isLoginSuccess($response);
    }

    public static function logout(){
        //TODO 看浏览器是否清楚缓存
        HttpClient::curl(GenerateUrlService::getLogoutUrl());
    }

    private static function isLoginSuccess(Array $array){
        return $array;
    }

    /**
     * 拼接url
     * $line
     */
    public static function setUrl($line = 'LINE_1'){
        $ip = Functions::getAttrValue($line);
        $userInfoUrl = Functions::getAttrValue('URL_LEFT_INFO');
        self::$userInfoUrl = 'http://'.$ip.$userInfoUrl;
    }

    /**
     * 左侧信息请求
     */
    public static function leftInfo(){
        $response = HttpClient::curl(self::$leftInfoUrl);
        var_dump($response);
        exit;
        return self::isGetLeftInfoSuccess($response);
    }

    /**
     * 获取左侧数据
     *
     * @param $string
     *
     * @return bool
     */
    private static function isGetLeftInfoSuccess($string){
        $arr = ['account', 'success', 'true', 'credit'];
        if(self::stringExist($string, $arr)){//成功
            Functions::saveLog(Yii::$app->message['leftInfo']['userInfoGetSuccess']);
            self::$userName = Functions::InterceptString($string, '{"account":"', '","credit"');    //用户名
            self::$edu = Functions::InterceptString($string, ',"credit":"', '","re_credit"');    //信用额度
            self::$yue = Functions::InterceptString($string, ',"total_amount":"', '","odds_refresh"');    //信用余额
            return false;
            //存数据库
        }else{  //失败
            Functions::saveLog(Yii::$app->message['leftInfo']['userInfoGetFailed']);
            return false;
        }
    }

    /**
     * 时时彩页面信息请求
     *
     * @param $data array post数据
     *
     * @return bool
     */
    public static function sscInfo($data = array()){
        if(empty($data)){
            $data = array('action' => 'ajax');
        }
        $response = HttpClient::curl(GenerateUrlService::getLoginUrl(), $data);
        return self::isGetSscInfo($response);
    }

    /**
     * 获取上期开奖结果，本期，赔率
     *
     * @param $string
     */
    private static function isGetSscInfo($string){
        $arr = ['integrate', 'success', 'true', 'changlong'];
        if(self::stringExist($string, $arr)){//成功
            Functions::saveLog(Yii::$app->message['sscList']['sscListGetSuccess']);
            self::$json = Functions::InterceptString($string, '"integrate":', ',"changlong"');    //赔率json
            self::$oldNum = Functions::InterceptString($string, '"timesold":"', '","resultnum"');    //上期期数
            self::$oldRes = Functions::InterceptString($string, 'resultnum":', ',"status');    //上期结果
            self::$nowNum = Functions::InterceptString($string, 'timesnow":"', '","timeclose');    //本期期数
            self::$v = Functions::InterceptString($string, 'version_number":"', '","game_limit');    //购买参数
            //存数据库
        }else{  //失败
            Functions::saveLog(Yii::$app->message['sscList']['sscListGetFailed']);
            return false;
        }
    }

    /**
     * 购买
     *
     * @param sring购买
     */
    public static function buy($data = ''){
        if(true){//判断是赔率是否购买
            Functions::saveLog(Yii::$app->message['Buy']['buying']);
            //查询本期是否购买
            $buy = array(
                't'=>'',
                'v'=>self::$v
            );
            $response = HttpClient::curl(self::$buyUrl, $buy);
            $arr = ['suc_orders', 'success', 'true'];
            if(self::stringExist($response, $arr)){  //成功
                Functions::saveLog(Yii::$app->message['Buy']['buySuccess']);
//                $data['user'] = self::$userName;
//                $data['edu'] = self::$edu;
//                $data['yue'] = self::$yue;
//                $data['one_price'] = self::$one_price;
//                $data['one'] = self::$one;
//                $data['two_price'] = self::$two_price;
//                $data['two'] = self::$two;
//                $data['three_price'] = self::$three_price;
//                $data['three'] = self::$three;
//                $data['four_price'] = self::$four_price;
//                $data['four'] = self::$four;
//                $data['five_price'] = self::$five_price;
//                $data['five'] = self::$five;
//                $data['all_price'] = self::$all_price;
//                $data['all'] = self::$all;
//                $data['json'] = self::$json;
//                $data['record_time'] = self::$record_time;
//                $data['old'] = self::$old;
//                $data['old_res'] = self::$old_res;
//                $data['now'] = self::$now;
//                $data['res_time'] = self::$res_time;
//                Record::insertRecord($data);
            }else{  //购买失败
            }
        }else{
            Functions::saveLog(Yii::$app->message['Buy']['noBuy']);
            return false;
        }
    }

    /**
     * 判断字符串是否有某些字符串
     */
    private static function stringExist($string, $arr = false){
        if(!$arr){
            return false;
        }
        foreach($arr as $row){
            if(!strstr($string, $row)){
                return false;
            }
        }
        return true;
    }
}