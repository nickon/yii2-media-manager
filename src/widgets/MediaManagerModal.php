<?php

namespace nickon\media_manager\widgets;

use yii;
use yii\helpers\ArrayHelper;
use yii\jui\Widget;
use nickon\media_manager\widgets\MediaManager;
use yii\bootstrap\Modal;

class MediaManagerModal extends Widget
{
    public $settings = [];
    public $defaultSettings = [];
    public $view;

    public static $init = false;

    public function init() {

        $this->defaultSettings = [
            'id' => uniqid(),
            'button' => [
                'label' => 'Загрузить файл',
                'class' => 'btn btn-default',
            ],
            'insertUrl' => '',
            'params' => [],
            'modal' => [
                'header' => [
                    'title' => 'Файловый менеджер'
                ],
                'footer' => [

                ]
            ]
        ];

        if (!empty($this->defaultSettings)) {
            $this->settings = ArrayHelper::merge( $this->defaultSettings, $this->settings );
        }

        Yii::setAlias('@media_manager', dirname( __DIR__ ));
        parent::init();
    }

    public function run(){

        $this->view = $this->getView();
        $id = uniqid();

        $params = $this->settings[ 'params' ];
        $params = htmlentities(json_encode( $params ));

        $footer = <<<HTML
<button type="button" class="btn btn-default media-manager-import-modal-btn" 
    data-id="{$id}"
    data-import-url="{$this->settings['insertUrl']}"
    data-params="{$params}">Импорт</button>
HTML;

        Modal::begin([
            'id' => $id,
            'toggleButton' => [
                'class' => 'media-manager-show-modal-btn btn btn-default',
                'data-id' => $id,
                'label' => $this->settings[ 'button' ][ 'label' ],
            ],
            'header' => '<h2>' . $this->settings['modal']['header']['title'] . '</h2>',
            'footer' => $footer,
            'size' => 'modal-xl',
        ]);




            $js = <<<JS
$(document).ready(function(){
    $('.modal-content .button-import-{$this->id}').click(function (){
        
        var box = $('.media_manager_files');
        var attach = $(box).find( "li.selected" );
        var file_id = $(attach).attr('data-id');
        
         if ( typeof file_id == 'undefined' ) {
            alert( 'Необходимо выбрать файл' );
        } else {
            var params = {$params};
            params.file_id = file_id;
            
            console.log(params);
            console.log(file_id);
            
            $.ajax({
                type: 'POST',
                url: '{$this->settings['insertUrl']}',
                dataType: 'json',
                data: params,
                success: function (){
                    $('.modal-dialog').modal('hide');
                    document.location.reload();
                }
            })
        }
    });
});
JS;

        //$this->view->registerJs($js);
        //$this->view->registerAssetBundle('MediaManagerAsset' );

        $this->view->registerAssetBundle( MediaManagerAsset::className() );


        Modal::end();
    }
}