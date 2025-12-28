<?php

declare(strict_types=1);

namespace app\components\opensearch\aggregationQueries;

use app\models\opensearch\BadmShipment;
use Throwable;
use Yii;
use yii\elasticsearch\Query;

class BadmRegionProductQuery extends BaseAggregationQuery
{
    public function getPage(array $afterKey = []): array
    {
        $aggregationOptions = [
            'composite' => [
                'size' => $this->pageSize,
                'sources' => [
                    ['region' => ['terms' => ['field' => 'region.keyword']]],
                    ['product_name' => ['terms' => ['field' => 'product_name.keyword']]],
                ],
            ],
            'aggs' => [
                'total_quantity' => [
                    'sum' => ['field' => 'quantity'],
                ],
            ],
        ];

        if ($afterKey) {
            $aggregationOptions['composite']['after'] = $afterKey;
        }

        try {
            $response = new Query()
                ->from(BadmShipment::index())
                ->addAggregate('region_product', $aggregationOptions)
                ->search(null, ['size' => 0, 'filter_path' => 'aggregations']);
        } catch (Throwable $exception) {
            Yii::error($exception);
            return [
                'buckets' => [],
                'after_key' => [],
            ];
        }

        $buckets = $response['aggregations']['region_product']['buckets'] ?? [];
        $afterKey = $response['aggregations']['region_product']['after_key'] ?? [];

        $result = [];
        foreach ($buckets as $b) {
            $result[] = [
                'region' => $b['key']['region'] ?? null,
                'product_name' => $b['key']['product_name'] ?? null,
                'quantity' => (float)($b['total_quantity']['value'] ?? 0),
            ];
        }

        return [
            'buckets' => $result,
            'after_key' => $afterKey,
        ];
    }

    public function getAll(): array
    {
        $all = [];
        $afterKey = [];
        do {
            $page = $this->getPage($afterKey);
            $all = array_merge($all, $page['buckets']);
            $afterKey = $page['after_key'];
        } while (!empty($afterKey));

        return $all;
    }
}
