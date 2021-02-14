<?php

namespace nickon\media_manager\helpers;

use Yii;
use yii\helpers\Html;

class StringHelper
{
        public static function cleanDouble( $value, $char = '-' ) {
            $search = $char . $char;

            do {
                $value = str_replace( $search, $char, $value );
            }
            while( mb_strpos( $value, $search ) !== false );

            return $value;
        }

        public static function translit( $value ) {

            $value = Yii::$app->transliter->translate( $value );
            $value = str_replace( ' ', '-', $value );
            $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value );

            $value = self::cleanDouble( $value, '-' );
            return $value;
        }
}