<?php
namespace app\controllers;

use app\service\Functions;
use yii;
use yii\web\Controller;
use app\service\WebSocket;
class DemoController extends Controller{

    public function actionIndex(){
        print_r(yii::$app->message['appStart']);
//        HttpClient::curl('http://sms.huhutv.com.cn/rtcrm-clientweb/npage/obim/staff/loginmng/initLogin.do');
//        HttpClient::downLoadCaptcha('http://sms.huhutv.com.cn/rtcrm-clientweb/npage/obim/staff/loginmng/queryRandomImage.do');
//        var_dump(HttpClient::curl('http://sms.huhutv.com.cn/rtcrm-clientweb/npage/obim/staff/loginmng/getValidSystemLogin.do',array(
//            'systemUserCode'=>'A-08',
//            'password'=>'Bb654321',
//            'valid'=>'N',
//            'randomWord'=>'8jkh'
//        )));
        exit;
    }

    public function actionSocket(){
        $address = 'localhost';
        $port = 8080;
        $socket = WebSocket::getInstance($address,$port);
        $socket->run();
    }

    public function actionPush(){
        return $this->render('log');
    }

    public function actionSend(){
        $address = 'localhost';
        $port = 8080;
        $socket = WebSocket::getInstance($address,$port);
        foreach($socket->sockets as $item){
            var_dump($socket->send($item,'sdsd'));
        }
    }
    public function actionLog(){
        Functions::saveLog("ee");
    }
}
