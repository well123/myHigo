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

    public function getDataByNum($num='') {
        $data = self::getInstance();
        $data -> num = $num;
        $res=$data->find()->asArray()->all();
        var_dump($res);
    }
}