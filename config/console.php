<?php

use yii\mongodb\console\controllers\MigrateController;

$config = [
    'id' => 'basic-console',
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@tests' => '@app/tests',
    ],
    'controllerMap' => [
        'migrate-mongodb' => [
            'class' => MigrateController::class,
            'migrationPath' => '@app/migrations/mongodb',
        ],
//        'fixture' => [ // Fixture generation command line.
//            'class' => 'yii\faker\FixtureController',
//        ],
    ],
];

return $config;
