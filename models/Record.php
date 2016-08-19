<?php
namespace app\models;
use yii\db\ActiveRecord;

class Record extends ActiveRecord{

    private static $record;

    public static function tableName() {
        return "buy_record";
    }

    public static function getInstance(){
        if(!(self::$record instanceof self)){
            self::$record = new self;
        }
        return self::$record;
    }

    //插入购买记录
    public static function insertRecord($data=array()){
        if(empty($data)){
            return false;
        }
        $record = new Record();
        $record -> n_id = $data['n_id'];
        $record -> ball_num = $data['ball_num'];
        $record -> ball_money = $data['ball_money'];
        $record -> ball_price = $data['ball_price'];
        $record -> ball_type = $data['ball_type'];
        $record -> buy_result = $data['buy_result'];
        $record -> insert();
        return $record ->attributes['id'];
    }

}
