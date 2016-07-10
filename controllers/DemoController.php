<?php
namespace app\controllers;

use yii;
use yii\web\Controller;
use app\service\HttpClient;

class DemoController extends Controller{

    public function actionIndex(){
//        HttpClient::curl('http://sms.huhutv.com.cn/rtcrm-clientweb/npage/obim/staff/loginmng/initLogin.do');
//        HttpClient::downLoadCaptcha('http://sms.huhutv.com.cn/rtcrm-clientweb/npage/obim/staff/loginmng/queryRandomImage.do');
        var_dump(HttpClient::curl('http://sms.huhutv.com.cn/rtcrm-clientweb/npage/obim/staff/loginmng/getValidSystemLogin.do',array(
            'systemUserCode'=>'A-08',
            'password'=>'Bb654321',
            'valid'=>'N',
            'randomWord'=>'8jkh'
        )));
        exit;
    }
}
