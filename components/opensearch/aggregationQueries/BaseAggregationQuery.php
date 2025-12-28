<?php

declare(strict_types=1);

namespace app\components\opensearch\aggregationQueries;

abstract class BaseAggregationQuery implements AggregationQuery
{
    protected int $pageSize;

    public function __construct(int $pageSize = 1000)
    {
        $this->pageSize = $pageSize;
    }
}
