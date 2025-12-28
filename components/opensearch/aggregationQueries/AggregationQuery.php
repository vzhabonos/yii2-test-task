<?php

declare(strict_types=1);

namespace app\components\opensearch\aggregationQueries;

interface AggregationQuery
{
    public function getPage(array $afterKey = []): array;

    public function getAll(): array;
}
