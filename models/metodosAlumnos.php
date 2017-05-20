<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\data\Pagination;



Class metodosAlumnos extends ActiveRecord{
    
    public function validarAlumnos($tabla, $model) {
        $count = $tabla->find()
                ->where(['nombre' => $model->nombre,
                    'apellidos' => $model->apellidos,
                    'clase' => $model->clase,
                    'nota_final' => $model->nota_final])
                ->count();
        return $count;
    }
    
    public function registrarAlumnos($tabla, $model) {
        
        $tabla->nombre = $model->nombre;
        $tabla->apellidos = $model->apellidos;
        $tabla->clase = $model->clase;
        $tabla->nota_final = $model->nota_final;
        $result->insert();
        
        return $result;
    }
    
    /*
    public function buscarAlumnos($tabla,$search) {
        $query = "SELECT * FROM alumnos WHERE id_alumno LIKE '%$search%' OR ";
        $query .="nombre LIKE '%$search%' OR apellidos LIKE '%$search%'";
        $result = $tabla->findBySql($query)->all();
        return $result;
        
    }
    */
    public function buscarAlumnos($tabla, $search) {
        $result = $tabla::find()
                        ->where(["like","id_alumno", $search])
                        ->orWhere(["like","nombre", $search])
                        ->orWhere(["like", "apellidos", $search]);
        return $result;
        
    }
    
    public function paginacion($numero, $total) {
         $pages = new Pagination([
                    "pageSize" => $numero,
                    "totalCount" => $total
                ]);
                return $pages;
    }
    
    public function buscarAlumnosPaginacion($tabla, $paginas) {
        $result = $tabla
                        ->offset($paginas->offset)
                        ->limit($paginas->limit)
                        ->all();
        return $result;
        
    }
    
    public function consultarAlumnos($tabla) {
        $result = $tabla::find();
        return $result;
    }
    
    public function eliminarAlumnos($tabla, $id) {
        $result = $tabla::deleteAll("id_alumno=:id_alumno", [":id_alumno" => $id]);
        return $result;
    }
    
    public function buscarModificarAlumnos($tabla, $id) {
        $result = $tabla::findOne($id);
        return $result;
    }
    
    public function mostrarModificarAlumnos($tabla, $model) {
        $model->id_alumno = $tabla->id_alumno;
        $model->nombre = $tabla->nombre;
        $model->apellidos = $tabla->apellidos;
        $model->clase = $tabla->clase;
        $model->nota_final = $tabla->nota_final;
        return $model;
    }
    
    public function ModificarAlumnos($tabla, $model) {
        $tabla->nombre = $model->nombre;
        $tabla->apellidos = $model->apellidos;
        $tabla->clase = $model->clase;
        $tabla->nota_final = $model->nota_final;
        $result = $tabla->update();
        return $result;
    }
}

