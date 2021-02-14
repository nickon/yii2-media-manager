<?php

namespace nickon\media_manager;
use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'nickon\media_manager\controllers';

    public function init() {
        parent::init();

        Yii::setAlias('@media_manager', __DIR__ );
    }
}