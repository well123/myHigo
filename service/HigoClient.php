<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 10:33
 */
namespace app\service;

use app\models\Num;
use yii;
use app\models\Record;
use app\models\Data;

class HigoClient{

    private static $v = '';          //购买参数
    private static $verifyValue = '';
    /** 存数据库数据 **/
    private static $json = '';
    private static $oldNum = '';    //上期期数
    private static $oldRes = '';    //上期结果
    private static $nowNum = '';    //本期期数是
    private static $nowTime = '';    //本期期数是
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
        self::$verifyValue =
            explode('_', HttpClient::curl(GenerateUrlService::getLoginKeyUrl().http_build_query($verifyParams)));
        $captionParams = [
            'systemversion' => InitService::getConfig('systemversion'),
            't' => self::$verifyValue[0]
        ];
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
     * 左侧信息请求
     */
    public static function leftInfo(){
        if(InitService::getSystemStatus() == 1){
            $response = HttpClient::curl(GenerateUrlService::getUserLeftInfo());
            return self::isGetLeftInfoSuccess($response);
        }else{
            Functions::saveLog(Yii::$app->message['service']['stop']);
        }
    }

    /**
     * 获取左侧数据
     *
     * @param $string
     *
     * @return bool
     */
    private static function isGetLeftInfoSuccess($string){
        $arr = [
            'account',
            'success',
            'true',
            'credit'
        ];
        if(self::stringExist($string, $arr)){//成功
            Functions::saveLog(Yii::$app->message['leftInfo']['userInfoGetSuccess']);
            self::$userName = Functions::InterceptString($string, '{"account":"', '","credit"');    //用户名
            self::$edu = Functions::InterceptString($string, ',"credit":"', '","re_credit"');    //信用额度
            self::$yue = Functions::InterceptString($string, ',"total_amount":"', '","odds_refresh"');    //信用余额
            return true;
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
        if(InitService::getSystemStatus() == 1){
            if(empty($data)){
                $data = array('action' => 'ajax');
            }
            $response = HttpClient::curl(GenerateUrlService::getOrderList(), $data);
            return self::isGetSscInfo($response);
        }else{
            Functions::saveLog(Yii::$app->message['service']['stop']);
        }
    }

    /**
     * 获取上期开奖结果，本期，赔率
     *
     * @param $string
     */
    private static function isGetSscInfo($string){
        $arr = [
            'integrate',
            'success',
            'true',
            'changlong'
        ];
        if(self::stringExist($string, $arr)){//成功
            Functions::saveLog(Yii::$app->message['sscList']['sscListGetSuccess']);
            self::$json = Functions::InterceptString($string, '"integrate":', ',"changlong"');    //赔率json
            self::$oldNum = Functions::InterceptString($string, '"timesold":"', '","resultnum"');    //上期期数
            $aOldRes = Functions::InterceptString($string, 'resultnum":', ',"status');    //上期结果
            self::$oldRes = implode(',', $aOldRes);
            self::$nowNum = Functions::InterceptString($string, 'timesnow":"', '","timeclose');    //本期期数
            self::$v = Functions::InterceptString($string, 'version_number":"', '","game_limit');    //购买参数
            $time = Functions::InterceptString($string, 'timeopen":', '},"oddSet');
            self::$nowTime = date('Y-m-d H:i:s', strtotime('+ '.$time.' seconds'));
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
    public static function buy(){
        if(InitService::getSystemStatus() == 1){
            Functions::saveLog(Yii::$app->message['Buy']['buying']);
            $arr = json_decode(self::$json, true);
            $res_data = self::getBuyData($arr);
            $buySuccess = '';
            if(!empty($res_data)){
                $buy = array();
                foreach($res_data as $value => $item){
                    $buy = array(
                        't' => $item,
                        'v' => self::$v
                    );
                    $response = HttpClient::curl(GenerateUrlService::getBuyUrl(), $buy);
                    $arr = [
                        'suc_orders',
                        'success',
                        'true'
                    ];
                    if(self::stringExist($response, $arr)){  //成功
                        //更新参数
                        $buySuccess .= $item;
                        self::$v = Functions::InterceptString($response, 'version_number":"', '","new_orders');
                        Functions::saveLog(Yii::$app->message['Buy']['buySuccess']);
                    }else{  //购买失败
                        Functions::saveLog($item.Yii::$app->message['Buy']['buyFailed']);
                    }
                }
                $aBuySuccess = explode(';', $buySuccess);
                self::insertRecord($aBuySuccess);
            }else{
                Functions::saveLog(self::$nowNum.Yii::$app->message['Buy']['nowNoBuy']);
            }
        }else{
            Functions::saveLog(Yii::$app->message['service']['stop']);
        }
    }

    //返回购买数据
    private static function getBuyData($price = array()){
        $buyArr = Data::getDataByNum(self::$nowNum);
        $data = array(
            $buyArr['one'],
            $buyArr['two'],
            $buyArr['three'],
            $buyArr['four'],
            $buyArr['fiver'],
            $buyArr['all']
        );
        $arr = array();
        $res = array();
        foreach($data as $key => $row){
            $arr = explode('|', $row);
            if($arr[0] != '-1'){
                $buy = explode(',', $arr[0]);
                $num = $key + 5;
                if(strlen($num.$buy[0]) == 3){
                    $p = '0'.$num.$buy[0];
                }else{
                    $p = '00'.$num.$buy[0];
                }
                if($price[$p] > InitService::getConfig('LOW_PRICE')){
                    $res[$buy[1]][] = substr($p, 0, 3).'|'.$buy[0].'|'.$price[$p].'|'.$buy[1];
                }else{
                    Functions::saveLog(substr($p, 0, 3).'|'.$buy[0].'|'.$price[$p].'|'.$buy[1].
                                       Yii::$app->message['Buy']['noBuy']);
                }
            }
            if($arr[1] != '-1'){
                $buy = explode(',', $arr[1]);
                $num = $key + 5;
                if(strlen($num.$buy[0]) == 3){
                    $p = '0'.$num.$buy[0];
                }else{
                    $p = '00'.$num.$buy[0];
                }
                if($price[$p] > InitService::getConfig('LOW_PRICE')){
                    $res[$buy[1]][] = substr($p, 0, 3).'|'.$buy[0].'|'.$price[$p].'|'.$buy[1];
                }else{
                    Functions::saveLog(substr($p, 0, 3).'|'.$buy[0].'|'.$price[$p].'|'.$buy[1].
                                       Yii::$app->message['Buy']['noBuy']);
                }
            }
        }
        $res_data = array();
        foreach($res as $key => $item){
            $res_data[$key] = implode(';', $res[$key]).';';
        }
        return $res_data;
    }

    private static function insertRecord($data = array()){
        $numData = array(
            'user' => self::$userName,
            'edu' => self::$edu,
            'yue' => self::$yue,
            'oldNum' => self::$oldNum,
            'oldRes' => self::$oldRes,
            'nowNum' => self::$nowNum,
            'nowTime' => self::$nowTime,
            'createTime' => date('Y-m-d H:i:S'),
            'json' => self::$json,
        );
        $n_id = Num::insertRecord($numData);
        foreach($data as $row){
            $aRow = explode('|', $row);
            $record = array(
                'n_id' => $n_id,
                'ball_num' => $aRow[0],
                'ball_money' => $aRow[3],
                'ball_price' => $aRow[2],
                'ball_type' => $aRow[1],
            );
            Record::insertRecord($record);
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