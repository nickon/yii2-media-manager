Yii2 Media Manager
=============
Pretty file manager

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist nickon/yii2-media-manager "dev-master"
```

or add

```
"nickon/yii2-media-manager": "dev-master"
```

to the require section of your `composer.json` file.

Configuartion
-------------


Add the following lines in your application configuration :

```php
'modules' => [
    // ...
    'media_manager' => [
        'class' => 'nickon\media_manager\Module',
    ],
],
```

Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \nickon\media_manager::widget(); ?>
```