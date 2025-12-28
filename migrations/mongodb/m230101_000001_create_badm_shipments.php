<?php

use app\models\mongo\BadmShipment;
use yii\mongodb\Migration;

class m230101_000001_create_badm_shipments extends Migration
{
    public function up(): void
    {
        $this->createCollection(BadmShipment::collectionName());
        $this->createIndex(
            BadmShipment::collectionName(),
            ['hash' => 1],
            ['unique' => true, 'name' => 'idx_hash_unique']
        );
    }

    public function down(): void
    {
        $this->dropCollection(BadmShipment::collectionName());
    }
}
