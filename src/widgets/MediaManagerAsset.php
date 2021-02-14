<?php

namespace nickon\media_manager\widgets;

use yii;
use yii\web\AssetBundle;

class MediaManagerAsset extends AssetBundle
{
    public $sourcePath = '@media_manager/assets';

    public $js = [
        'js/dropzone/dist/min/dropzone.min.js',
        'js/media_manager.js',
        'js/media_manager_modal.js',

    ];

    public $css = [
        'js/dropzone/dist/min/dropzone.min.css',
        'css/media_manager.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset'
    ];
}