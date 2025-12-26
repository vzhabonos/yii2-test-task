<?php

use yii\elasticsearch\Connection;

return [
    'class' => Connection::class,
    'nodes' => require __DIR__ . '/elastic-nodes.php',
    'dslVersion' => $_ENV['ELASTICSEARCH_DSL'] ?? 5,
];
