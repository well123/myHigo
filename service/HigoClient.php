<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/10 0010
 * Time: 下午 10:33
 */
namespace app\service;

use app\models\Config;
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
    private static $seal_time = '';    //封盘时间
    private static $lottery_time = '';    //开奖时间
    private static $n_id = '';    //期数外键
    /** 用户信息 **/
    private static $userName = '';
    private static $edu = '';
    private static $yue = '';
    private static $paid_pre_period = '';
    private static $time = 0;

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
            'PicId' => 0
        ];
        for($i = 0; $i < 60; $i++){
            if($result['error'] != 0){
                $captionImg = HigoClient::getCaption();
                $caption = new \Caption();
                $result = $caption->CJY_RecognizeBytes($captionImg);
            }else{
                break;
            }
        }
        return $result;
    }

    public static function login(){
        $caption = self::getCaptionStr();
        if($caption['error'] != 0){
            Functions::saveLog(Yii::$app->message['login']['getCaptionFailed']);
            return false;
        }
        $params = [
            'VerifyCode' => $caption['caption'],
            '__VerifyValue' => self::$verifyValue[1],
            '__name' => InitService::getConfig('USERNAME'),
            'password' => InitService::getConfig('PASSWORD'),
            'isSec' => InitService::getConfig('isSec'),
            'cid' => InitService::getConfig('cid'),
            'cname' => InitService::getConfig('cname'),
            'systemversion' => InitService::getConfig('systemversion')
        ];
        $response = HttpClient::curl(GenerateUrlService::getLoginUrl(), $params, GenerateUrlService::getIndexUrl());
        $response = self::isLoginSuccess($response, $caption['PicId']);
        if($response['status']){
            return true;
        }else{
            if($response['error'] == Yii::$app->message['login']['verifyFailed']){
                if(self::$time < 100){
                    self::login();
                }else{
                    return false;
                }
            }
        }
    }

    public static function logout(){
        HttpClient::curlFetch(GenerateUrlService::getLogoutUrl());
    }

    private static function isLoginSuccess($response, $PicId = 0){
        $result = [
            'status' => 0,
            'error' => ''
        ];
        $data = explode("\n", $response);
        if(sizeof($data) > 2){
            $config = Config::findOne(['name' => 'LOGGED_FRONT_PART']);
            $config->value = $data[0];
            $config->save();
            $referUrl = str_replace('host', GenerateUrlService::getUrlFrontPart(), $data[1]);
            HttpClient::curlFetch($referUrl, [], GenerateUrlService::getIndexUrl());
            $result['status'] = 1;
            return $result;
        }else{
            Functions::saveLog(Yii::$app->message['login']['loginFailed'].$response);
            if(strpos($response, Yii::$app->message['login']['verifyFailed']) !== false){
                $caption = new \Caption();
                $caption->CJY_ReportError($PicId);
                self::$time++;
                $result['error'] = Yii::$app->message['login']['verifyFailed'];
                Functions::saveLog(Yii::$app->message['login']['retryLogin']);
            }
            return $result;
        }
    }

    /**
     * @param bool $showInfo
     * 左侧信息请求
     *
     * @return bool
     */
    public static function leftInfo($showInfo = true){
        if(InitService::getSystemStatus() == 1){
            $response = HttpClient::curl(GenerateUrlService::getUserLeftInfo());
            return self::isGetLeftInfoSuccess($response, $showInfo);
        }else{
            Functions::saveLog(Yii::$app->message['service']['stop']);
        }
    }

    /**
     * @param $string
     * @param $showInfo
     * 获取左侧数据
     *
     * @return bool
     */
    private static function isGetLeftInfoSuccess($string, $showInfo){
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
            self::$yue = Functions::InterceptString($string, 're_credit":"', '","total_amount');    //信用余额
            self::$paid_pre_period =
                Functions::InterceptString($string, 'total_amount":"', '","odds_refresh');    //已下金额
            return true;
            //存数据库
        }else{  //失败
            $showInfo && Functions::saveLog(Yii::$app->message['leftInfo']['userInfoGetFailed']);
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
        $time = 0;
        while($time < 2){
            if(InitService::getSystemStatus() == 1){
                if(empty($data)){
                    $data = array('action' => 'ajax');
                }
                $response = HttpClient::curl(GenerateUrlService::getOrderList(), $data);
                if(self::isGetSscInfo($response)){
                    break;
                }
            }else{
                Functions::saveLog(Yii::$app->message['service']['stop']);
            }
            $time++;
            if($time >= 2){
                Functions::saveLog(Yii::$app->message['Buy']['nowPrice']);
                Login::logout();
            }
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
            if(strlen(self::$json) < 10){
                Functions::saveLog(Yii::$app->message['Buy']['nowPrice']);
                return false;
            }
            self::$oldNum = Functions::InterceptString($string, '"timesold":"', '","resultnum"');    //上期期数
            $aOldRes = Functions::InterceptString($string, 'resultnum":', ',"status');    //上期结果
            $oldRes = json_decode($aOldRes, true);
            self::$oldRes = implode('', $oldRes);
            self::$nowNum = Functions::InterceptString($string, 'timesnow":"', '","timeclose');    //本期期数
            self::$v = Functions::InterceptString($string, 'version_number":"', '","game_limit');    //购买参数
            $timeClose = Functions::InterceptString($string, 'timeclose":', ',"timeopen');        //封盘时间
            $timeOpen = Functions::InterceptString($string, 'timeopen":', '},"oddSet');        //开奖时间
            self::$seal_time = intval($timeClose / 60).':'.($timeClose % 60);                    //分秒形式
            self::$lottery_time = intval($timeOpen / 60).':'.($timeOpen % 60);
            $numData = array(
                'user' => self::$userName,
                'edu' => self::$edu,
                'yue' => self::$yue,
                'paid_pre_period' => self::$paid_pre_period,
                'oldNum' => self::$oldNum,
                'oldRes' => self::$oldRes,
                'nowNum' => self::$nowNum,
                'seal_time' => self::$seal_time,
                'lottery_time' => self::$lottery_time,
                'json' => self::$json,
            );
            self::$n_id = Num::insertRecord($numData);
            //存数据库
        }else{  //失败
            Functions::saveLog(Yii::$app->message['sscList']['sscListGetFailed']);
            return false;
        }
        return true;
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
                        $sItem = self::translation($item);
                        self::$v = Functions::InterceptString($response, 'version_number":"', '","new_orders');
                        Functions::saveLog($sItem.Yii::$app->message['Buy']['buySuccess']);
                    }else{  //购买失败
                        Functions::saveLog($item.Yii::$app->message['Buy']['buyFailed']);
                        //因为网络问题购买失败，插入购买记录
                        $item = trim($item, ';');
                        $aItem = explode(';', $item);
                        foreach($aItem as $row){
                            $aRow = explode('|', $row);
                            $onBuyRecord = array(
                                'n_id' => self::$n_id,
                                'ball_num' => $aRow[0],
                                'ball_money' => $aRow[3],
                                'ball_price' => $aRow[2],
                                'ball_type' => $aRow[1],
                                'buy_result' => -2,
                            );
                            Record::insertRecord($onBuyRecord);
                        }
                    }
                }
                $buySuccess = trim($buySuccess, ';');
                $aBuySuccess = explode(';', $buySuccess);
                self::insertRecord($aBuySuccess);
                Functions::saveLog(self::$nowNum.Yii::$app->message['Buy']['finished']);    //购买完成
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
        if(empty($buyArr)){
            return array();
        }
        $data = array(
            $buyArr['one'],
            $buyArr['two'],
            $buyArr['three'],
            $buyArr['four'],
            $buyArr['five'],
            $buyArr['all_type']
        );
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
                if($price[$p] >= InitService::getConfig('LOW_PRICE')){
                    $res[$buy[1]][] = substr($p, 0, 3).'|'.$buy[0].'|'.$price[$p].'|'.$buy[1];
                }else{
                    Functions::saveLog(substr($p, 0, 3).Yii::$app->message['Buy']['noBuy']);
                    //把赔率过低的插入购买记录
                    $onBuyRecord = array(
                        'n_id' => self::$n_id,
                        'ball_num' => substr($p, 0, 3),
                        'ball_money' => $buy[1],
                        'ball_price' => $price[$p],
                        'ball_type' => $buy[0],
                        'buy_result' => -1,
                    );
                    Record::insertRecord($onBuyRecord);
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
                    //把赔率过低的插入购买记录
                    $onBuyRecord = array(
                        'n_id' => self::$n_id,
                        'ball_num' => substr($p, 0, 3),
                        'ball_money' => $buy[1],
                        'ball_price' => $price[$p],
                        'ball_type' => $buy[0],
                        'buy_result' => -1,
                    );
                    Record::insertRecord($onBuyRecord);
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
        if(empty($data)){
            return false;
        }
        foreach($data as $row){
            $aRow = explode('|', $row);
            $record = array(
                'n_id' => self::$n_id,
                'ball_num' => $aRow[0],
                'ball_money' => $aRow[3],
                'ball_price' => $aRow[2],
                'ball_type' => $aRow[1],
                'buy_result' => 1,
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

    /**
     * 翻译成中文
     */
    private static function translation($string = ''){
        if($string == ''){
            return false;
        }
        $string = trim($string, ';');
        $arr = explode(';', $string);
        $res_data = '';
        foreach($arr as $row){
            $aRow = explode('|', $row);
            $ball = $aRow[0].$aRow[1];
            $res_data .= '<'.Yii::$app->message['ball'][$ball].'>';
        }
        return $res_data;
    }
}
