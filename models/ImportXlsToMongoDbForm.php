<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportXlsToMongoDbForm extends Model
{
    public UploadedFile|string|null $file = null;

    public function rules(): array
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => ['xls', 'xlsx'], 'checkExtensionByMimeType' => false],
        ];
    }

    public function import(): array|false
    {
        if (!$this->validate()) {
            return false;
        }

        $spreadsheet = IOFactory::load($this->file->tempName);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return ['imported' => 0, 'skipped' => 0];
        }

        $headers = array_shift($rows);

        $mapping = $this->getFieldMap();

        $collection = Yii::$app->mongodb->getCollection('import_data');

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $doc = [];

            foreach ($headers as $col => $title) {
                if (!$title) {
                    continue;
                }
                $key = $mapping[$title] ?? null;
                if ($key) {
                    $doc[$key] = trim($row[$col] ?? '');
                }
            }

            if (empty($doc)) {
                $skipped++;
                continue;
            }

            $uniqueFilter = [
                'invoice_date' => $doc['invoice_date'] ?? null,
                'client_code' => $doc['client_code'] ?? null,
                'product_code' => $doc['product_code'] ?? null,
                'qty' => $doc['qty'] ?? null,
            ];

            $result = $collection->update($uniqueFilter, $doc, ['upsert' => true]);

            if (!empty($result['upserted'])) {
                $imported++;
            } else {
                $skipped++;
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
        ];
    }

    private function getFieldMap(): array
    {
        return [
            'Фирма' => 'firm',
            'Область' => 'region',
            'Город' => 'city',
            'Дата накл' => 'invoice_date',
            'Факт.адрес доставки' => 'delivery_address',
            'Юр. адрес клиента' => 'legal_address',
            'Клиент' => 'client_name',
            'Код клиента' => 'client_code',
            'Код подразд кл' => 'client_branch_code',
            'ОКПО клиента' => 'client_okpo',
            'Лицензия' => 'license',
            'Дата окончания лицензии' => 'license_valid_until',
            'Код товара' => 'product_code',
            'Штрих-код товара' => 'product_barcode',
            'Товар' => 'product_name',
            'Код мориона' => 'morion_code',
            'ЕИ' => 'unit',
            'Производитель' => 'manufacturer',
            'Поставщик' => 'supplier',
            'Количество' => 'qty',
            'Склад/филиал' => 'warehouse',
        ];
    }
}
