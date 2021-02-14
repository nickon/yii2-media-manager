<?php

namespace nickon\media_manager\controllers;

use Faker\Provider\File;
use Imagine\Image\ManipulatorInterface;
use yii;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\Controller;
use nickon\media_manager\models\Thumb;

class ThumbController extends Controller
{
    private $root_dir = '@frontend/web/uploads/media_manager';
    private $root_url = '/uploads/media_manager';
    private $fs;

    public function actionThumb() {

        ini_set('memory_limit', '1024M' );

        $path = Yii::$app->request->get('path', false );
        $size = Yii::$app->request->get('size', 'thumbs' );

        $this->root_dir = Yii::getAlias( $this->root_dir );
        $this->fs = \Yii::createObject([
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path'  => $this->root_dir,
        ]);

        $thumb_dir = $this->root_dir . '/_cache';
        if (!file_exists($thumb_dir) && !FileHelper::createDirectory( $thumb_dir)) return false;

        $file_name = $this->root_dir . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($file_name)) return false;

        $thumb_name = $thumb_dir . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $path;
        $thumb_dir = dirname( $thumb_name );
        if (!file_exists($thumb_dir) && !FileHelper::createDirectory($thumb_dir)) return false;

        if ( !file_exists( $thumb_name )) {
            $stream = $this->fs->readStream( $path );
            $image = Image::thumbnail( $stream, 150, 150, ManipulatorInterface::THUMBNAIL_INSET );
            fclose($stream);

            if (!$image || !$image->save( $thumb_name )) {
                throw new \yii\web\NotFoundHttpException();
            }
        }

        $type = FileHelper::getMimeType( $thumb_name );

        header('Content-type: ' . $type );
        header('Content-Length: ' . filesize( $thumb_name ));
        readfile( $thumb_name );
        die();
    }

}