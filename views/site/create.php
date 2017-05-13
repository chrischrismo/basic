<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<a href="<?= Url::toRoute("site/view")?>">Ir a la lista de alumnos</a>

<h1>Crear Alumno</h1>

<?php $form = ActiveForm::begin([
    "method" => "post",
    'enableClientValidation' => true,
]);
?>
<div class="form-group">
 <?= $form->field($model, "nombre")->input("text") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "apellidos")->input("text") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "clase")->input("text") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "nota_final")->input("text") ?>   
</div>

<?= Html::submitButton("Crear", ["class" => "btn btn-primary"]) ?>

<?php $form->end() ?>
<br>
<h3 class="<?= $color ?>"><?= $msg ?></h3>