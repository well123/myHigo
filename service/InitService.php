<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/15 0015
 * Time: 16:28
 */
namespace app\service;

use yii;
use app\models\Config;

class InitService{

    public static function getConfig($param){
        $data = Yii::$app->cache->get($param);
        if($data === false){
            $configs = Config::find()->asArray()->all();
            foreach($configs as &$item){
                Yii::$app->cache->set($item['name'], $item['value']);
            }
            $data = self::getConfig($param);
        }
        return $data;
    }

    public static function run(){
        self::initConfig();
        Login::login();
    }

    private static function initConfig(){
        $html = HigoClient::getIndexPageContent();
        //系统版本
        $config = Config::findOne(['name' => 'systemversion']);
        preg_match_all('|systemversion = "(.*)"|U', $html, $out, PREG_PATTERN_ORDER);
        $config->value = $out[1][0];
        !empty($config->value) && $config->save();
        //cid
        $config = Config::findOne(['name' => 'cid']);
        preg_match_all('|name="cid" value="(.*)"|U', $html, $out, PREG_PATTERN_ORDER);
        $config->value = $out[1][0];
        !empty($config->value) && $config->save();
        //cname
        $config = Config::findOne(['name' => 'cname']);
        preg_match_all('|name="cname" value="(.*)"|U', $html, $out, PREG_PATTERN_ORDER);
        $config->value = $out[1][0];
        !empty($config->value) && $config->save();
    }

    public static function changeSystemStatus(){
        $config = Config::findOne(['name' => 'system_status']);
        $config->value = $config->value ? 0 : 1;
        $config->save();
    }

    public static function getSystemStatus(){
        $config = Config::findOne(['name' => 'system_status']);
        return $config->value ? true : false;
    }
}