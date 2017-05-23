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


///12 Registro de usuarios
use app\models\FormRegister;
use app\models\Users;

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
                
                $count = $metodos->validarAlumnos($table,$model);
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
                    $result = $metodos->registrarAlumnos($table,$model);
                    
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

    
    /// 8 borrar en BD
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
    
    
    /// 9 Modificar BD
    public function actionUpdate() 
    {
        $alumnos = new Alumnos;
        $metodos = new metodosAlumnos();
        $model = new FormAlumnos;
        $msg = null;
        $color = null;
        
        if($model->load(Yii::$app->request->post()))
        {
            if($model->validate())
            {
                
                $table = $metodos->buscarModificarAlumnos($alumnos, $model->id_alumno);
               
                
                if($table)
                {
                    /*
                    $table->nombre = $model->nombre;
                    $table->apellidos = $model->apellidos;
                    $table->clase = $model->clase;
                    $table->nota_final = $model->nota_final;
                  */
                    $result = $metodos->ModificarAlumnos($table, $model);
                    if($result)
                    {
                        $msg = "El alumno ha sido actualizado correctamente";
                        $color = "text-success";
                    }
                    else
                    {
                        $msg = "El Alumno no ha podido ser actualizado";
                        $color = "text-danger";
                    }
                    
                }
                else 
                {
                    $msg = "El alumno seleccionado no ha sido encontrado";
                    $color = "text-danger";
                }
            }
            else
            {
                $model->getErrors();
            }
        }
        
        if(Yii::$app->request->get("id_alumno"))
        {
            $id_alumno = Html::encode($_GET["id_alumno"]);
            if ((int) $id_alumno)
            {
                
                $table = $metodos->buscarModificarAlumnos($alumnos, $id_alumno);
                
                if($table)
                {
                    
                    $model = $metodos->mostrarModificarAlumnos($table, $model);
                    
                    /*
                    $model->id_alumno = $table->id_alumno;
                    $model->nombre = $table->nombre;
                    $model->apellidos = $table->apellidos;
                    $model->clase = $table->clase;
                    $model->nota_final = $table->nota_final;
                    */
                }
                else 
                {
                    return $this->redirect(["site/view"]);
                }
            }
            else
            {
                return $this->redirect(["site/view"]);
            }
        }
        else 
        {
            return $this->redirect(["site/view"]);
        }
        return $this->render("update", ["model" => $model, "msg" => $msg, "color" => $color]);
    }
    
    
    ///// 12 Registrar, validar y mandar correo de usuario
    
    private function randKey($str='', $long=0) //crea claves aleatorias
    {
        $key = null;
        $str = str_split($str);
        $start = 0;
        $limit = count($str)-1;
        for($x=0; $x<$long; $x++)
        {
            $key .= $str[rand($start, $limit)];
        }
        return $key;
    }
  
 public function actionConfirm()
 {
    $table = new Users;
    if (Yii::$app->request->get())
    {
   
        //Obtenemos el valor de los parámetros get
        $id = Html::encode($_GET["id"]);
        $authKey = $_GET["authKey"];
    
        if ((int) $id)
        {
            //Realizamos la consulta para obtener el registro
            $model = $table
            ->find()
            ->where("id=:id", [":id" => $id])
            ->andWhere("authKey=:authKey", [":authKey" => $authKey]);
 
            //Si el registro existe
            if ($model->count() == 1)
            {
                $activar = Users::findOne($id);
                $activar->activate = 1;
                if ($activar->update())
                {
                    echo "Enhorabuena registro llevado a cabo correctamente, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
                }
                else
                {
                    echo "Ha ocurrido un error al realizar el registro, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
                }
             }
            else //Si no existe redireccionamos a login
            {
                return $this->redirect(["site/login"]);
            }
        }
        else //Si id no es un número entero redireccionamos a login
        {
            return $this->redirect(["site/login"]);
        }
    }
 }
 
 public function actionRegister()
 {
  //Creamos la instancia con el model de validación
  $model = new FormRegister;
   
  //Mostrará un mensaje en la vista cuando el usuario se haya registrado
  $msg = null;
   
  //Validación mediante ajax
  if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax)
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
   
  //Validación cuando el formulario es enviado vía post
  //Esto sucede cuando la validación ajax se ha llevado a cabo correctamente
  //También previene por si el usuario tiene desactivado javascript y la
  //validación mediante ajax no puede ser llevada a cabo
  if ($model->load(Yii::$app->request->post()))
  {
   if($model->validate())
   {
    //Preparamos la consulta para guardar el usuario
    $table = new Users;
    $table->username = $model->username;
    $table->email = $model->email;
    //Encriptamos el password
    $table->password = crypt($model->password, Yii::$app->params["salt"]);
    //Creamos una cookie para autenticar al usuario cuando decida recordar la sesión, esta misma
    //clave será utilizada para activar el usuario
    $table->authKey = $this->randKey("abcdef0123456789", 200);
    //Creamos un token de acceso único para el usuario
    $table->accessToken = $this->randKey("abcdef0123456789", 200);
     
    //Si el registro es guardado correctamente
    if ($table->insert())
    {
     //Nueva consulta para obtener el id del usuario
     //Para confirmar al usuario se requiere su id y su authKey
     $user = $table->find()->where(["email" => $model->email])->one();
     $id = urlencode($user->id);
     $authKey = urlencode($user->authKey);
      
     $subject = "Confirmar registro";
     $body = "<h1>Haga click en el siguiente enlace para finalizar tu registro</h1>";
     $body .= "<a href='http://localhost:8080/basic/web/index.php?r=site/confirm&id=".$id."&authKey=".$authKey."'>Confirmar</a>";
      
     //Enviamos el correo
     Yii::$app->mailer->compose()
     ->setTo($user->email)
     ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
     ->setSubject($subject)
     ->setHtmlBody($body)
     ->send();
     
     $model->username = null;
     $model->email = null;
     $model->password = null;
     $model->password_repeat = null;
     
     $msg = "Enhorabuena, ahora sólo falta que confirmes tu registro en tu cuenta de correo";
    }
    else
    {
     $msg = "Ha ocurrido un error al llevar a cabo tu registro";
    }
     
   }
   else
   {
    $model->getErrors();
   }
  }
  return $this->render("register", ["model" => $model, "msg" => $msg]);
 }
    
    ////////
    
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
