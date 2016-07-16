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
        if(!empty($data)){
            return false;
        }
        $record = self::getInstance();
        $record -> user = $data['user'];
        $record -> edu = $data['edu'];
        $record -> yue = $data['yue'];
        $record -> one_price = $data['one_price'];
        $record -> one = $data['one'];
        $record -> two_price = $data['two_price'];
        $record -> two = $data['two'];
        $record -> three_price = $data['three_price'];
        $record -> three = $data['three'];
        $record -> four_price = $data['four_price'];
        $record -> four = $data['four'];
        $record -> five_price = $data['five_price'];
        $record -> five = $data['five'];
        $record -> all_price = $data['all_price'];
        $record -> all = $data['all'];
        $record -> json = $data['json'];
        $record -> record_time = date("Y-m-d H:i:s");
        $record -> old = $data['old'];
        $record -> old_res = $data['old_res'];
        $record -> now = $data['now'];
        $record -> res_time = $data['res_time'];
        $record ->save();
        return true;
    }

}