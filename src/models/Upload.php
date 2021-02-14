<?php

namespace nickon\media_manager\models;

use nickon\media_manager\helpers\StringHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;


class Upload extends Model
{
    public $path;
    public $file;
    public $fs;
    public $unique = true;

    public $allowedImageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];
    public $allowedFileExtensions  = ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip', 'rar', '7zip', 'txt'];

    public function rules() {
        return [
            [[ 'path', 'file' ], 'required', 'strict' => true ],
            ['path', 'string' ],
            ['path', 'validatePath'],
            [[ 'file' ],
                'file',
                'skipOnEmpty' => false,
            ]
        ];
    }

    public function formName() {
        return '';
    }

    private function getFileName( $file ) {

            $baseName = StringHelper::translit( $file->baseName );

            if ( $this->unique ) {
                   $file_name = $this->path . DIRECTORY_SEPARATOR . md5( microtime(true)) . '.' . $file->extension;
            } else $file_name = $this->path . DIRECTORY_SEPARATOR . $baseName . '.'. $file->extension;

            $count = 1;
            while ( $this->fs->has( $file_name )) {
                 $file_name = $this->path . DIRECTORY_SEPARATOR . $baseName . '_' . $count . '.' . $file->extension;
                 $count ++;
            }

            return $file_name;
    }

    public function upload() {

        if ( $this->validate()) {
            $file = $this->file;
            $path = $this->getFileName( $file );

            $is_image = in_array( $file->extension, $this->allowedImageExtensions ) ? 1 : 0;
            if ( !in_array( $file->extension, ArrayHelper::merge( $this->allowedImageExtensions, $this->allowedFileExtensions ))) return false;

            if ( $stream = fopen( $file->tempName, 'r+' )) {
                 $write = $this->fs->writeStream( $path, $stream );
                 fclose( $stream );

                 if ( $write ) {
                      $db = \Yii::$app->db;

                      $db->createCommand()
                          ->insert('media_manager', [
                                'name'        => basename( $path ),
                                'title'       => $file->baseName,
                                'description' => '',
                                'extension'   => $file->extension,
                                'size'        => $this->fs->getSize( $path ),
                                'path'        => $this->path,
                                'type'        => $file->type,
                                'is_image'    => $is_image,
                          ])
                          ->execute();

                     return true;
                 }
            }

            return false;
        }
    }

    public function validatePath($attribute, $params) {
        $this->$attribute = FileHelper::normalizePath( $this->$attribute, DIRECTORY_SEPARATOR );
    }
}