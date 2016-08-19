<?php
namespace app\models;
use yii\db\ActiveRecord;

class Num extends ActiveRecord{

    private static $num;

    public static function tableName() {
        return "Num";
    }

    public static function getInstance(){
        if(!(self::$num instanceof self)){
            self::$num = new self;
        }
        return self::$num;
    }

    //插入购买记录
    public static function insertRecord($data=array()){
        if(empty($data)){
            return false;
        }
        //判断当前期数是否有记录，有则更新，否则插入
        $num = new Num();
        $num = $num -> find()->where(['nowNum'=>$data['nowNum']])->one();
        if(empty($num)){
            //插入
            $num = new Num();
            $num -> user =  $data['user'];
            $num -> edu = $data['edu'];
            $num -> yue = $data['yue'];
            $num -> paid_pre_period = $data['paid_pre_period'];
            $num -> oldNum = $data['oldNum'];
            $num -> oldRes = $data['oldRes'];
            $num -> nowNum = $data['nowNum'];
            $num -> seal_time = $data['seal_time'];
            $num -> lottery_time = $data['lottery_time'];
            $num -> json = $data['json'];
            $num -> insert();
            return $num->attributes['id'];
        }
        //保存
        $num -> user =  $data['user'];
        $num -> edu = $data['edu'];
        $num -> yue = $data['yue'];
        $num -> paid_pre_period = $data['paid_pre_period'];
        $num -> oldNum = $data['oldNum'];
        $num -> oldRes = $data['oldRes'];
        $num -> nowNum = $data['nowNum'];
        $num -> seal_time = $data['seal_time'];
        $num -> lottery_time = $data['lottery_time'];
        $num -> json = $data['json'];
        $num -> save();
        return $num->attributes['id'];
    }

}
