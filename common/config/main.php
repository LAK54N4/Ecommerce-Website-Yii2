<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'locale' => 'id_ID',
        'timeZone' => 'Asia/Jakarta',
        'dateFormat' => 'dd-MM-yyyy hh:mm:ss',
        'thousandSeparator' => '.',
        'decimalSeparator' => ',',
        'currencyCode' => 'Rp',
        ],
    ],
];
