<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2016/6/28
 * Time: 21:24
 */
namespace app\models;

use yii;
use yii\db\ActiveRecord;

class Log extends ActiveRecord{

    private static $log;

    public static function getInstance(){
        if(!(self::$log instanceof self)){
            self::$log = new self;
        }
        return self::$log;
    }

}