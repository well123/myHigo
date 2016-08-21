<?php
namespace app\controllers;

use app\models\Log;
use app\service\Functions;
use app\service\HigoClient;
use app\service\InitService;
use app\service\Login;
use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Data;

class SiteController extends Controller{

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => ['logout' => ['post'],],
            ],
        ];
    }

    public function actions(){
        return [
            'error' => ['class' => 'yii\web\ErrorAction',],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : NULL,
            ],
        ];
    }

    public function actionIndex(){
        if(Yii::$app->user->isGuest){
            return $this->actionLogin();
        }
        InitService::run();
        return $this->render('log');
    }

    public function actionLogin(){
        if(!Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()){
            return $this->goBack();
        }
        return $this->render('login', ['model' => $model,]);
    }

    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionContact(){
        $model = new ContactForm();
        if($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])){
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', ['model' => $model,]);
    }

    public function actionLogs(){
        $log = new Log();
        $logs = $log->find()->orderBy('id DESC')->limit(100)->asArray()->all();
        echo json_encode($logs);
    }

    public function actionAbout(){
        return $this->render('about');
    }

    public function actionChangeStatus(){
        $status = $status = Yii::$app->request->get('status');
        InitService::changeSystemStatus($status);
    }

    public function actionGetStatus(){
        exit(json_encode(InitService::getSystemStatus()));
    }

    public function actionGetContent(){
        InitService::initConfig();
        if(Login::login()){
            Functions::saveLog("登录成功");
            $sleep = rand(10,60);
            sleep($sleep);
            HigoClient::leftInfo(false);
            HigoClient::leftInfo();
            HigoClient::sscInfo();
            $sleep = rand(300,360);
            sleep($sleep);
            HigoClient::buy();
            $sleep = rand(10,20);
            sleep($sleep);
            HigoClient::leftInfo();
            HigoClient::sscInfo();
        }
        Login::logout();
    }
}
