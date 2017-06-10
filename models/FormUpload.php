<?php

namespace app\models;
use yii\base\model;

class FormUpload extends model{
    
    public $file;
    
    public function rules() {
        return [
            ['file', 'file',
             'skipOnEmpty' => false,
             'uploadRequired' => 'No has seleccionado ningun archivo',
             'maxSize' => 1024*1024*1, //1 MB
             'tooBig' => 'El tamaño maximo permitido es 1 MB',
             'minSize' => 10, //10 bytes
             'tooSmall' => 'El tamaño minimo permitido son 10 BYTES',
             'extensions' => 'pdf, txt, doc',
             'wrongExtension' => 'El archivo {file} no contiene una extension permitida {extensions}',
             'maxFiles' => 4,
             'tooMany' => 'El maximo de archivos permitidos son {limit}',
            ],
        ];
    }
    
    public function attributeLabels() {
        return [
            'file' => 'Seleccionar archivos:',
        ];
    }
    
}

