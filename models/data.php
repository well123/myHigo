<?php
namespace app\models;
use yii\db\ActiveRecord;

class Data extends ActiveRecord{

    private static $record;

    public static function getInstance(){
        if(!(self::$record instanceof self)){
            self::$record = new self;
        }
        return self::$record;
    }

    public static function getDataByNum($num='') {
        $data = new Data();
        $res=$data->find()->where(['num_periods'=>$num])->asArray()->one();
        return $res;
    }
}