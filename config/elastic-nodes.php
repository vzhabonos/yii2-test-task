<?php

$maxElasticNodes = $_ENV['MAX_ELASTICSEARCH_NODES'] ?? 10;

$servers = [];
for ($i = 1; $i <= $maxElasticNodes; $i++) {
    $prefix = "ELASTICSEARCH_NODE_{$i}_";
    $hostVariableName = $prefix . 'HOST';
    if ($i === 1 && !isset($_ENV[$hostVariableName])) {
        $prefix = 'ELASTICSEARCH_NODE_';
        $hostVariableName = $prefix . 'HOST';
    }
    $portVariableName = $prefix . 'PORT';
    $host = $_ENV[$hostVariableName] ?? null;
    $port = $_ENV[$portVariableName] ?? 9200;
    if (!empty($host)) {
        $servers[] = ['http_address' => "$host:$port"];
    }
}

return $servers;
