<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;


Class metodosAlumnos extends ActiveRecord{
    
    public function validarAlumnos($tabla,$nombre,$apellidos,$clase,$nota_final) {
        $count = $tabla->find()
                ->where(['nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'clase' => $clase,
                    'nota_final' => $nota_final])
                ->count();
        return $count;
    }
    
    public function registrarAlumnos($tabla,$nombre,$apellidos,$clase,$nota_final) {
        $tabla->nombre = $nombre;
        $tabla->apellidos = $apellidos;
        $tabla->clase = $clase;
        $tabla->nota_final = $nota_final;
        $result = $tabla->insert();
        return $result;
    }
    
    
    public function buscarAlumnos($tabla,$search) {
        $query = "SELECT * FROM alumnos WHERE id_alumno LIKE '%$search%' OR ";
        $query .="nombre LIKE '%$search%' OR apellidos LIKE '%$search%'";
        $result = $tabla->findBySql($query)->all();
        return $result;
        
    }
}

