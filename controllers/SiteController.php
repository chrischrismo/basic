<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
// 3
use app\models\ValidarFormulario;

///4
use app\models\ValidarFormularioAjax;
use yii\widgets\ActiveForm;
use yii\web\Response;

class SiteController extends Controller
{
    
    
    //////////////1 conectar acción vista
    public function actionSaluda($get = "Tutorial Yii")
    {
        $mensaje = "Hola Mundo";
        $numeros = [0,1,2,3,4,5];
        return $this->render("saluda", 
                [
                    "saluda" => $mensaje,
                    "numeros" => $numeros,
                    "get" => $get,
                ]);
    }
    
    
    /////////2 conectar vista-acción
    public function actionFormulario($mensaje = null)
    {
        return $this->render("formulario", ["mensaje" => $mensaje]);
    }
    
    public function actionRequest()
    {
        $mensaje = null;
        if(isset($_REQUEST["nombre"]))
        {
            $mensaje = "bien, has enviado tu nombre correctamente: ".$_REQUEST["nombre"];
        }
        $this->redirect(["site/formulario", "mensaje" => $mensaje]);
    }
    
    
    ///////////3 validacion cliente servidor
    public function actionValidarformulario()
    {
        $model = new ValidarFormulario();
        if($model->load(Yii::$app->request->post())){ //si se envia correctamente el formulario
            if($model->validate()){ //si la validacion es correcta
                //Por ejemplo, consultar en una base de datos.
            }
            else
            {
                $model->getErrors();
            }
        }
        return $this->render("validarformulario", ["model" => $model]);
    }
    
    ///////////4 validacion ajax
    
    public function actionValidarformularioajax() {
        $model = new ValidarFormularioAjax();
        $msg = null;
        if($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax){ //si se envia correctamente el formulario y si la peticion es ajax
            Yii::$app->response->format = Response::FORMAT_JSON; //convierte respuesta en formato json
            return ActiveForm::validate($model); //valida el formulario
        }
        
        if ($model->load(Yii::$app->request->post())){ //si se envia correctamente el formulario
            if ($model->validate()){ //si la validacion es correcta
                //Por ejemplo hacer una consulta a una base de datos
                $msg = "Enhorabuena formulario enviado correctamente";
                $model->nombre = null;
                $model->email = null;
            }
            else{
                $model->getErrors();
            }
                
            
        }
        return $this->render("validarformularioajax", ['model' => $model, 'msg' => $msg]);
    }



    public function behaviors()
    {
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
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
