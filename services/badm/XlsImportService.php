<?php

declare(strict_types=1);

namespace app\services\badm;

use app\models\mongo\BadmShipment;
use yii\helpers\Json;
use PhpOffice\PhpSpreadsheet\IOFactory;
use MongoDB\Driver\Exception\BulkWriteException;
use yii\mongodb\Exception as YiiMongodbException;

class XlsImportService
{
    private const array HEADERS_MAPPING = [
        'Фирма' => 'company',
        'Область' => 'region',
        'Город' => 'city',
        'Дата накл' => 'delivery_date',
        'Факт.адрес доставки' => 'address_fact',
        'Юр. адрес клиента' => 'address_legal',
        'Клиент' => 'client_name',
        'Код клиента' => 'client_code',
        'Код подразд кл' => 'client_sub_code',
        'ОКПО клиента' => 'client_okpo',
        'Лицензия' => 'license',
        'Дата окончания лицензии' => 'license_expiration',
        'Код товара' => 'product_code',
        'Штрих-код товара' => 'barcode',
        'Товар' => 'product_name',
        'Код мориона' => 'morion_code',
        'ЕИ' => 'unit',
        'Производитель' => 'manufacturer',
        'Поставщик' => 'supplier',
        'Количество' => 'quantity',
        'Склад/филиал' => 'warehouse',
    ];

    private const int BATCH_SIZE = 1000;

    /**
     * @throws YiiMongodbException
     */
    public function importXlsToMongo(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestDataRow() - 1;
        $highestCol = $sheet->getHighestDataColumn();

        $headerRow = $sheet->rangeToArray("A1:{$highestCol}1")[0];
        $headers = $this->normalizeHeaders($headerRow);

        $inserted = 0;
        $skipped = 0;

        $batch = [];

        for ($row = 2; $row <= $highestRow; $row++) {
            $values = $sheet->rangeToArray("A$row:$highestCol$row")[0];
            $doc = array_combine($headers, $values);

            $doc['hash'] = md5(Json::encode($doc));
            $batch[] = $doc;

            if ($row < $highestRow && count($batch) < self::BATCH_SIZE) {
                continue;
            }

            [$insertedBatch, $skippedBatch] = $this->insertBatch($batch);
            $inserted += $insertedBatch;
            $skipped += $skippedBatch;
            $batch = [];
        }

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
        ];
    }

    /**
     * @throws YiiMongodbException
     */
    private function insertBatch(array $docs): array
    {
        try {
            $result = BadmShipment::getCollection()->batchInsert($docs, ['ordered' => false]);
            return [count($result), 0];
        } catch (YiiMongodbException $exception) {
            $previous = $exception->getPrevious();
            if (!($previous instanceof BulkWriteException)) {
                throw $exception;
            }
            $writeResult = $previous->getWriteResult();
            return [
                $writeResult->getInsertedCount(),
                count($writeResult->getWriteErrors())
            ];
        }
    }

    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $h) {
            $normalized[] = self::HEADERS_MAPPING[$h] ?? strtolower(trim(preg_replace('/\s+/', '_', $h)));
        }

        return $normalized;
    }
}

