<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

// 3 validacion 
use app\models\ValidarFormulario;

///4 validacion ajax
use app\models\ValidarFormularioAjax;
use yii\widgets\ActiveForm;
use yii\web\Response;

///5 crear registros
use app\models\FormAlumnos;
use app\models\Alumnos;

///6 busqueda y lectura de registros
use app\models\FormSearch;
use yii\helpers\Html;

///7 paginacion
use yii\data\Pagination;

///8 eliminar con redireccionamiento
use yii\helpers\Url;

/// metodos y consultas
use app\models\metodosAlumnos;


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
    
    //////////5 Insertar en BD

    public function actionCreate() {
        $model = new FormAlumnos;
        $msg = null;
        $color = null;
        $count = null;
        
        if($model->load(Yii::$app->request->post()))
        {
            if($model->validate())
            {
                $table = new Alumnos();
                $metodos = new metodosAlumnos();
                
                $count = $metodos->validarAlumnos($table,$model->nombre,$model->apellidos,$model->clase,$model->nota_final);
                /*
                $count = $table->find()
                ->where(['nombre' => $model->nombre,
                    'apellidos' => $model->apellidos,
                    'clase' => $model->clase,
                    'nota_final' => $model->nota_final])
                ->count();
                */
                
                if($count > 0){
                    $msg = "El registro ya existe";
                    $color = "text-danger";
                }
                else 
                {
                    $result = $metodos->registrarAlumnos($table,$model->nombre,$model->apellidos,$model->clase,$model->nota_final);
                    
                    /*
                    $table->nombre = $model->nombre;
                    $table->apellidos = $model->apellidos;
                    $table->clase = $model->clase;
                    $table->nota_final = $model->nota_final;
                    $table->insert()
                    */
                    
                
                        if($result > 0)
                        {
                        $msg = "Enhorabuena registro guardado correctamente";
                        $color = "text-success";
                        $model->nombre = null;
                        $model->apellidos = null;
                        $model->clase = null;
                        $model->nota_final = null;
                    
                        }
                        else
                        {
                        $msg = "Ha ocurrido un error al insertar el registro";
                        $color = "text-danger";
                        }
                     
                     
                }
                
                
                
            }
            else
            {
                $model->getErrors();
            }
        }
        return $this->render("create", ['model' => $model, 'msg' => $msg, 'color' => $color]);
    }
    
    ////6 consultar y buscar en BD, 7 paginacion
    public function actionView() 
    {
        $form = new FormSearch;
        $search = null;
        $metodos = new metodosAlumnos();
        $alumnos = new Alumnos;
        
        if($form->load(Yii::$app->request->get()))
        {
            if ($form->validate())
            {
                if($form->q == '')
                {
                $table = $metodos->consultarAlumnos($alumnos);
                $table_clone = clone $table;
                $count = $table_clone->count();
                $pages = $metodos->paginacion(5, $count);
                $model = $metodos->buscarAlumnosPaginacion($table, $pages);
                }
                else
                {
                $search = Html::encode($form->q);
                $table = $metodos->buscarAlumnos($alumnos,$search);
                $table_clone = clone $table;
                $count = $table_clone->count();
                $pages = $metodos->paginacion(2,$count);
                $model = $metodos->buscarAlumnosPaginacion($table, $pages); 
                }
            }
            else
            {
                $form->getErrors();
            }
        }
        else
        {
                $table = $metodos->consultarAlumnos($alumnos);
                $table_clone = clone $table;
                $count = $table_clone->count();
                $pages = $metodos->paginacion(5, $count);
                $model = $metodos->buscarAlumnosPaginacion($table, $pages);
        }
        
        
        /*
        $table = new Alumnos;
        $metodos = new metodosAlumnos();
        $model = $table->find()->all();
        
        $form = new FormSearch;
        $search = null;
        if($form->load(Yii::$app->request->get()))
        {
            if($form->validate())
            {
                $search = Html::encode($form->q);
                
                $model = $metodos->buscarAlumnos($table,$search);
            }
            else
            {
                $form->getErrors();
            }
        }
         */
        
        return $this->render("view", ['model' => $model, 'form' => $form, 'search' => $search, "pages" => $pages]);
    }

    public function actionDelete() {
        if (Yii::$app->request->post())
        {
            $alumnos = new Alumnos;
            $metodos = new metodosAlumnos();
            $id_alumno = Html::encode($_POST["id_alumno"]);
            
            if((int) $id_alumno)
            {
            
                if($metodos->eliminarAlumnos($alumnos, $id_alumno))
                {
                    echo ("Alumno con id $id_alumno eliminado con éxito, redireccionando ...");
                    echo ("<meta http-equiv='refresh' content='3; ".Url::toRoute("site/view")."'>");
                }
                else
                {
                echo ("Ha ocurrido un error al eliminar el alumno, redireccionando ...");
                echo ("<meta http-equiv='refresh' content='3; ".Url::toRoute("site/view")."'>");
                }
            }
            else 
            {
                echo ("Ha ocurrido un error al eliminar el alumno, redireccionando ...");
                echo ("<meta http-equiv='refresh' content='3; ".Url::toRoute("site/view")."'>");
            }
        }
        else 
        {
            return $this->redirect(["site/view"]);
        }
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
