<?php

namespace nickon\media_manager\widgets;

use nickon\media_manager\models\Files;
use yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class MediaManager extends Widget
{
        public $id;
        public $settings = [];
        public $defaultSettings = [];
        public $view;
        public $baseUrl = '/media_manager/ajax/';

        public function init(){

            $this->defaultSettings = [
                'api' => [
                    'uploadUrl' => Url::to([ $this->baseUrl . 'upload' ]),
                    'listUrl'   => Url::to([ $this->baseUrl . 'list' ]),
                    'deleteUrl' => Url::to([ $this->baseUrl . 'delete' ]),
                    'updateUrl' => Url::to([ $this->baseUrl . 'update' ]),
                    'searchUrl' => Url::to([ $this->baseUrl . 'search' ]),
                    'filterUrl' => Url::to([ $this->baseUrl . 'filter' ]),
                ],

                'unique' => false,
                'path'   => ''
            ];

            Yii::setAlias('@media_manager', dirname( __DIR__ ));

            if (!empty($this->defaultSettings)) {
                $this->settings = ArrayHelper::merge( $this->defaultSettings, $this->settings );
            }

            parent::init();
        }

        public function run(){

            $this->view = $this->getView();
            MediaManagerAsset::register( $this->view );

            $params = [
                'path'   => $this->settings[ 'path' ],
                'unique' => $this->settings[ 'unique' ] ? 1: 0,
            ];

            $params = Json::encode($params);

            $js = <<<JS
$(document).ready(function(){
    media_manager.init( '{$this->id}', {
        'path': '{$this->settings['path']}',
        'api': {
            'uploadUrl': '{$this->settings['api']['uploadUrl']}',
            'listUrl'  : '{$this->settings['api']['listUrl']}',
            'deleteUrl': '{$this->settings['api']['deleteUrl']}',
            'updateUrl': '{$this->settings['api']['updateUrl']}',
            'searchUrl': '{$this->settings['api']['searchUrl']}'
        },
        'dropzoneConfig' : {
            'params': {$params}
        }
    })
    .list('{$this->settings['path']}');
    
});
JS;
            $this->view->registerJs($js, yii\web\View::POS_END );

            $dates = Files::find()
                ->groupBy('YEAR(date) DESC, MONTH(date) DESC' )
                ->select( 'YEAR(date) as year, MONTH(date) as month' )
                ->asArray()
                ->all();

            return $this->render('media_manager', [
                'id'    => $this->id,
                'dates' => $dates
            ]);
        }

        protected function hasModel() {
            return $this->model instanceof Model && $this->attribute !== null;
        }
}