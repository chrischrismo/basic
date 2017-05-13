<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<a href="<?= Url::toRoute("site/create") ?>">Crear un nuevo alumno</a>

<?php $f = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("site/view"),
    "enableClientValidation" => true,
]);
?>

<div class="form-group">
<?= $f->field($form,"q")->input("search") ?>
</div>

<?= Html::submitButton("Buscar", ['class'=> 'btn btn-primary']) ?>

<?php $f->end() ?>

<h3><?= $search ?></h3>

<h3>Lista de alumnos</h3>
<table class="table table-bordered">
    <tr>
        <th>Id Alumno</th>
        <th>Nombre</th>
        <th>Apellidos</th>
        <th>Clase</th>
        <th>Nota Final</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($model as $row): ?>
    <tr>
        <th><?= $row->id_alumno ?></th>
        <th><?= $row->nombre ?></th>
        <th><?= $row->apellidos ?></th>
        <th><?= $row->clase ?></th>
        <th><?= $row->nota_final ?></th>
        <th><a href="#">Editar</a></th>
        <th><a href="#">Eliminar</a></th>
    </tr>
    <?php endforeach ?>
</table>