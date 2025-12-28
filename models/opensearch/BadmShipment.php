<?php

declare(strict_types=1);

namespace app\models\opensearch;

use yii\elasticsearch\ActiveRecord;

/**
 * @property string $region
 * @property string $product_name
 * @property float $quantity
 * @property string $hash
 */
class BadmShipment extends ActiveRecord
{
    public static function index(): string
    {
        return 'badm_shipments' . (YII_ENV_TEST ? '_test' : '');
    }

    public function attributes(): array
    {
        return [
            'region',
            'product_name',
            'quantity',
            'hash',
            'company',
            'city',
            'client_name',
            'manufacturer',
            'warehouse',
        ];
    }
}
