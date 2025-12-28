<?php

use yii\mongodb\Connection;

return [
    'class' => Connection::class,
    'dsn' => sprintf('mongodb://%s:%s/%s_test', $_ENV['MONGODB_HOST'], $_ENV['MONGODB_PORT'], $_ENV['MONGODB_DATABASE']),
    'options' => [
        "username" => $_ENV['MONGODB_USER'],
        "password" => $_ENV['MONGODB_PASSWORD'],
    ]
];
