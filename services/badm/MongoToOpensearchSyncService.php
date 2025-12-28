<?php

declare(strict_types=1);

namespace app\services\badm;

use app\models\mongo\BadmShipment as BadmShipmentMongo;
use app\models\opensearch\BadmShipment as BadmShipmentOpensearch;
use yii\base\InvalidConfigException;
use yii\elasticsearch\BulkCommand;
use yii\elasticsearch\Exception;

class MongoToOpensearchSyncService
{
    private const int BATCH_SIZE = 1000;

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function syncAll(): array
    {
        $collection = BadmShipmentMongo::getCollection();

        $cursor = $collection->find([], [], [
            'batchSize' => self::BATCH_SIZE,
        ]);

        $processed = 0;

        $batch = [];
        foreach ($cursor as $doc) {
            $batch[] = $doc;

            if (count($batch) >= self::BATCH_SIZE) {
                $this->pushBatchToOpenSearch($batch);
                $processed += count($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $result = $this->pushBatchToOpenSearch($batch);
            $processed += count($result['items']);
        }

        BadmShipmentOpensearch::getDb()->createCommand()->refreshIndex(BadmShipmentOpensearch::index());

        return [
            'processed' => $processed,
        ];
    }

    private function pushBatchToOpenSearch(array $docs): mixed
    {
        $bulkCommand = new BulkCommand([
            'db' => BadmShipmentOpensearch::getDb(),
            'index' => BadmShipmentOpensearch::index()
        ]);
        foreach ($docs as $doc) {
            $bulkCommand->addAction([
                'index' => [
                    '_id' => $doc['hash'],
                ]
            ], [
                'company' => $doc['company'] ?? null,
                'region' => $doc['region'] ?? null,
                'city' => $doc['city'] ?? null,
                'client_name' => $doc['client_name'] ?? null,
                'product_name' => $doc['product_name'] ?? null,
                'quantity' => (float)($doc['quantity'] ?? 0),
                'manufacturer' => $doc['manufacturer'] ?? null,
                'warehouse' => $doc['warehouse'] ?? null,
            ]);
        }

        return $bulkCommand->execute();
    }
}
