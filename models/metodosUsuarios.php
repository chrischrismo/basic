<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;



Class metodosUsuarios extends ActiveRecord{
    
    public function buscarEmailUsuario($tabla, $email) {
        $result = $tabla::find()->where("email=:email", [":email" => $email]);
        return $result;
    }
    
    public function buscarEmailIdUsuario($tabla, $email) {
        $result = $tabla::find()->where("email=:email", [":email" => $email])->one();
        return $result;
    }
    
    public function buscarEmailIdVerificacionUsuario($tabla, $email, $id_recover, $verification_code) {
        $result = $tabla::findOne(["email" => $email, "id" => $id_recover, "verification_code" => $verification_code]);
        
        return $result;
    }
}
