<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env', '.env.test']);
$dotenv->safeLoad();

defined('YII_DEBUG') or define('YII_DEBUG', (bool) $_ENV['APP_DEBUG']);
defined('YII_ENV') or define('YII_ENV', $_ENV['APP_ENV']);

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
