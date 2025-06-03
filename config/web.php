<?php

$config = [
    'id' => 'jpg-to-pdf-converter',
    'name' => 'JPG to PDF Converter',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'jpg-to-pdf-converter-secret-key-change-in-production',
            'enableCsrfValidation' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'converter/index',
                'convert' => 'converter/convert',
                'download' => 'converter/download',
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
    ],
    'params' => [
        'uploadPath' => '@app/uploads',
        'maxFileSize' => 10 * 1024 * 1024, // 10MB
        'allowedExtensions' => ['jpg', 'jpeg'],
    ],
];

if (YII_ENV_DEV && class_exists('yii\debug\Module')) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '0.0.0.0/0'],
    ];
}

if (YII_ENV_DEV && class_exists('yii\gii\Module')) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '0.0.0.0/0'],
    ];
}

return $config;