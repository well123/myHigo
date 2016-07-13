<?php
namespace app\controllers;

use app\service\Functions;
use app\service\Login;
use app\service\HigoClient;
use yii;
use yii\web\Controller;
use app\service\WebSocket;
use app\service\WebSocket2;
use app\service\EchoServer;

class DemoController extends Controller{

    private $server;

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

    public function actionLogin(){
        var_dump(Login::getScore());
//        Login::login();
    }
    public function actionSocket(){
        set_time_limit(0);
        $address = 'localhost';
        $port = 8080;
        //        $socket = WebSocket::getInstance($address, $port);
        //        $socket->run();
        $this->server = new EchoServer($address, $port);
        try{
            $this->server->run();
        }catch(Exception $e){
            $this->server->stdout($e->getMessage());
        }
    }

    public function actionPush(){
        return $this->render('log');
    }

    public function actionSend(){
        $address = 'localhost';
        $port = 8080;
        //        $socket = WebSocket::getInstance($address, $port);
        //        $socket->connect($socket->master);
        //        var_dump($socket->users);
        //        var_dump($socket->send($socket->master, 'sdsd'));
        $this->send();
    }

    public function actionLog(){
        Functions::saveLog("ee");
    }
    private function send(){
        $address = 'localhost';
        $port = 8080;
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "OK.\n";
        }

        echo "Attempting to connect to '$address' on port '$port'...";
        $result = socket_connect($socket, $address, $port);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            echo "OK.\n";
        }

        $in = "HEAD / HTTP/1.1\r\n";
        $in .= "Host: www.example.com\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $out = '';

        echo "Sending HTTP HEAD request...";
        socket_write($socket, $in, strlen($in));
        echo "OK.\n";
    }
}
