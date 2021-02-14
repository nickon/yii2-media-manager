<?php


namespace nickon\media_manager\models;

use yii\db\ActiveRecord;

class Files extends ActiveRecord
{
        public static function tableName() {
            return 'media_manager';
        }
}