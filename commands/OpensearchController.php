<?php

declare(strict_types=1);

namespace app\commands;

use app\models\opensearch\BadmShipment;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Json;

class OpensearchController extends Controller
{
    public function actionDropBadmShipmentsIndex(): void
    {
        try {
            $result = Yii::$app->elasticsearch->createCommand()->deleteIndex(BadmShipment::index());
            $this->stdout(Json::encode($result) . "\n", Console::FG_GREEN);
        } catch (Throwable $e) {
            $this->stdout($e->getMessage() . "\n", Console::FG_RED);
        }
    }
}
