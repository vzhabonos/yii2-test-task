<?php

$commonConfig = require(__DIR__ . '/common.php');

/**
 * Application configuration shared by all test types
 */
$config = [
    'id' => 'basic-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ]
];

return yii\helpers\ArrayHelper::merge(
    $commonConfig,
    $config,
);
