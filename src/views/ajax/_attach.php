<?php

    foreach ( $files as $file ) {

        if ( $file[ 'is_image' ] == 1 ) {
             $img_url = $file['path'] . DIRECTORY_SEPARATOR . $file['name'];
             $img_url = \yii\helpers\Url::to([ '/media_manager/thumb/thumb', 'path' => $img_url, 'size' => 'thumb' ]);

        } else {
             $img_url = '/uploads/media_manager/document.png';
        }

        $file_name = Yii::getAlias( '@frontend/web/uploads/media_manager' )
            . DIRECTORY_SEPARATOR
            . $file[ 'path' ]
            . DIRECTORY_SEPARATOR . $file[ 'name' ];

        $file_size = filesize( $file_name );
        $file_size = \Yii::$app->formatter->asShortSize( $file_size ) ;
        $description = \yii\helpers\Html::encode( $file['description']);

        $url = '/uploads/media_manager/' . $file[ 'path' ] . DIRECTORY_SEPARATOR . $file['name'];

        echo <<<HTML
<li class="attach" 
    data-id="{$file['id']}" 
    data-path="{$file['path']}"
    data-title="{$file['title']}" 
    data-name="{$file['name']}"
    data-date="{$file['date']}"
    data-size="{$file_size}"
    data-description="{$description}"
    data-url="{$url}"
    data-is-image="{$file['is_image']}">
    <div class="preview">
        <div class="thumb">
            <div class="centered">
                <img src="{$img_url}" draggable="false" alt="" />
            </div>
        </div>
        <div class="filename">
            <div>{$file['name']}</div>
        </div>
    </div>
</li>
HTML;

    }