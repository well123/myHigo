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
                Yii::$app->set($item['name'], $item['value']);
            }
            $data = self::getConfig($param);
        }
        return $data;
    }
}