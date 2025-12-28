<?php

use yii\bootstrap5\LinkPager as Bootstrap5LinkPager;
use yii\debug\Module as YiiDebugModule;
use yii\log\FileTarget;
use yii\caching\FileCache;
use yii\widgets\LinkPager;

$params = require __DIR__ . '/params.php';
$elastic = require __DIR__ . '/elastic.php';
$mongodb = require __DIR__ . '/mongodb.php';

$config = [
    'name' => 'Yii2 Test Task',
    'language' => 'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'user' => null,
        'cache' => [
            'class' => FileCache::class,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mongodb' => $mongodb,
        'elasticsearch' => $elastic,
    ],
    'container' => [
        'definitions' => [
            LinkPager::class => Bootstrap5LinkPager::class,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => YiiDebugModule::class,
        'allowedIPs' => ['*.*.*.*'],
    ];
}

return $config;
