<?php
/**
 * Created by PhpStorm.
 * User: wuBin
 * Date: 2016/7/15 0015
 * Time: 16:06
 */
namespace app\models;

use yii;
use yii\db\ActiveRecord;

class Config extends ActiveRecord{
    private static $config;

    public static function getInstance(){
        if(!(self::$config instanceof self)){
            self::$config = new self;
        }
        return self::$config;
    }
}