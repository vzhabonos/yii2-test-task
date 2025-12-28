<?php

declare(strict_types=1);

namespace app\models\mongo;

use yii\mongodb\ActiveRecord;

class BadmShipment extends ActiveRecord
{
    public static function collectionName(): string
    {
        return 'badm_shipments';
    }

    public function attributes(): array
    {
        return [
            '_id',
            'company',
            'region',
            'city',
            'delivery_date',
            'address_fact',
            'address_legal',
            'client_name',
            'client_code',
            'client_sub_code',
            'client_okpo',
            'license',
            'license_expiration',
            'product_code',
            'barcode',
            'product_name',
            'morion_code',
            'unit',
            'manufacturer',
            'supplier',
            'quantity',
            'warehouse',
            'hash',
        ];
    }
}
