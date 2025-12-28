<?php

declare(strict_types=1);

namespace app\components\opensearch;

use app\components\opensearch\aggregationQueries\AggregationQuery;
use yii\data\BaseDataProvider;

class AggregationQueryDataProvider extends BaseDataProvider
{
    public function __construct(
        private readonly AggregationQuery $aggregationQuery,
        array $config = []
    ) {
        parent::__construct($config);
    }

    protected function prepareModels(): array
    {
        $models = $this->aggregationQuery->getAll();

        $sort = $this->getSort();
        if ($sort !== false) {
            $orders = $sort->getOrders();
            foreach ($orders as $attribute => $direction) {
                usort($models, static function ($a, $b) use ($attribute, $direction) {
                    if ($direction === SORT_REGULAR || $a[$attribute] === $b[$attribute]) {
                        return 0;
                    }
                    if ($direction === SORT_DESC) {
                        return ($a[$attribute] < $b[$attribute] ? -1 : 1);
                    }
                    return ($a[$attribute] > $b[$attribute] ? -1 : 1);
                });
            }
        }

        $pagination = $this->getPagination();
        if ($pagination !== false) {
            $pagination->totalCount = count($models);
            $models = array_slice($models, $pagination->getOffset(), $pagination->getLimit());
        }

        return $models;
    }

    protected function prepareKeys($models): array
    {
        return array_keys($models);
    }

    protected function prepareTotalCount(): int
    {
        return count($this->aggregationQuery->getAll());
    }
}
