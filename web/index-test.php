<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env', '.env.test']);
$dotenv->safeLoad();

defined('YII_DEBUG') or define('YII_DEBUG', (bool) $_ENV['APP_DEBUG']);
defined('YII_ENV') or define('YII_ENV', $_ENV['APP_ENV']);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/test.php';

(new yii\web\Application($config))->run();
