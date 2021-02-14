<?php

namespace nickon\media_manager\controllers;

use Faker\Provider\File;
use MongoDB\BSON\TimestampInterface;
use nickon\media_manager\models\Files;
use nickon\media_manager\models\Upload;
use nickon\media_manager\widgets\MediaManagerAsset;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;


class AjaxController extends Controller
{
    private $root_dir = '@frontend/web/uploads/media_manager';
    private $root_url = '/uploads/media_manager';
    private $fs;
    private $request;

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        $this->request = \Yii::$app->getRequest();
        if ( $this->request->method == 'OPTIONS' ) return [];

        $this->root_dir = FileHelper::normalizePath( \Yii::getAlias( $this->root_dir ), DIRECTORY_SEPARATOR );

        $this->fs = \Yii::createObject([
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path'  => $this->root_dir,
        ]);

        return parent::beforeAction($action);
    }

    public function actionSearch(){
        $search = \Yii::$app->request->post( 'search', '' );
        if ( $search == '' ) return [ 'result' => '' ];

        $files = Files::find()
            ->where([ 'like', 'title', '%' . $search . '%', false ])
            ->orWhere([ 'like', 'description', '%' . $search . '%', false ])
            ->orderBy('date DESC' )
            ->asArray()
            ->all();

        $content = $this->renderPartial( '_attach', [ 'files' => $files ]);
        return [ 'result' => $content ];
    }

    public function actionList() {
        $limit = 54;

        $page = \Yii::$app->request->post( 'page', 1 );

        $page = intval( $page );
        if ( $page <= 0 ) $page = 1;

        $offset = ( $page - 1 ) * $limit;
        $path = \Yii::$app->request->post('path', false );

        if ( $path AND $path != '' AND $path != '0' ) {

            $data  = explode( '-', $path );
            $year  = intval( $data[0] );
            $month = intval( $data[1] );

            $count = Files::find()
                ->where( 'YEAR(date) = :year AND MONTH(date) = :month', [ ':year' => $year, ':month' => $month ])
                ->count();

            $files = Files::find()
                ->where( 'YEAR(date) = :year AND MONTH(date) = :month', [ ':year' => $year, ':month' => $month ])
                ->orderBy('date DESC' )
                ->offset( $offset )->limit( $limit )
                ->asArray()->all();
        } else {
            $count = Files::find()->count();
            $files = Files::find()
                ->orderBy( 'date DESC' )
                ->offset( $offset )->limit( $limit )
                ->asArray()->all();
        }

        $can_load = ( $offset < $count );

        $content = $this->renderPartial( '_attach', [ 'files' => $files ]);
        return [
            'result' => $content,
            'can_load' => $can_load,
            'offset' => $offset,
            'limit' => $limit,
            'page'  => $page,
            'path' => $path,
            'count' => $count,
        ];
    }

    public function actionUpload() {

        $path   = \Yii::$app->request->post('path', false );
        $unique = \Yii::$app->request->post( 'unique', 1 );
        $unique = $unique == 1;

        $model         = new Upload();
        $model->file   = UploadedFile::getInstanceByName('file' );
        $model->path   = $path;
        $model->fs     = $this->fs;
        $model->unique = $unique;

        if ( $this->request->isPost ) {

             if ( !in_array( $model->file->extension, ArrayHelper::merge( $model->allowedImageExtensions, $model->allowedFileExtensions ))) {
                 return [ 'error' => false, 'message' => 'file extension is not allowed' ];
             }

             if ( $model->upload() ) {
                  return [ 'error' => false, 'success' => true ];
             }
             else return [ 'error' => true ];
        }
        else return [ 'error' => true ];
    }

    public function actionDelete() {

        $selected = \Yii::$app->request->post('selected', []);
        if ( is_array( $selected )) {
             foreach( $selected as $id ) {

                 $file = Files::find()->where( 'id = :id', [ ':id' => $id ])->one();

                 $file_name = $this->root_dir . DIRECTORY_SEPARATOR . $file[ 'path' ] . DIRECTORY_SEPARATOR . $file[ 'name' ];
                 if ( file_exists( $file_name )) {
                     unlink( $file_name );

                     $thumb_name = $this->root_dir . '/_cache/thumb/' . $file[ 'path' ] . DIRECTORY_SEPARATOR . $file_name[ 'name' ];
                     if ( file_exists( $thumb_name )) {
                         unlink($thumb_name);
                     }

                     $file->delete();
                 }
             }
             return [
                 'error'   => false,
                 'success' => true,
                 'deleted' => $selected,
             ];
        }

        return [ 'error' => true, 'success' => false, 'deleted' => []];
    }

    public function actionUpdate() {
        $id = intval( \Yii::$app->request->post( 'id', 0 ));
        if ( $id == 0 ) return [ 'error' => 'empty id' ];

        $title = trim( \Yii::$app->request->post('title',''));
        $description = trim( \Yii::$app->request->post('description', '' ));

        $file = Files::find()->where( 'id = :id', [ ':id' => $id ])->one();
        if ( !$file ) return [ 'error' => true, 'message' => 'file not found' ];

        $file->title = $title;
        $file->description = $description;
        $file->save(false);

        return [ 'error' => false, 'success' => true ];
    }

    public function actionWidget() {

         $id   = uniqid();
         $view = $this->getView();
         MediaManagerAsset::register( $view );

         return [
             'id'      => $id,
             'content' => $this->renderAjax( '_widget', [ 'id' => $id ]),
         ];
    }

    public function actionDownload() {

    }


}