<?php

declare(strict_types=1);

namespace app\models\forms;

use app\services\badm\XlsImportService;
use yii\base\Model;
use yii\web\UploadedFile;

class BadmImportForm extends Model
{
    /** @var UploadedFile */
    public $file;

    public function rules(): array
    {
        return [
            [['file'], 'required'],
            [
                ['file'],
                'file',
                'extensions' => ['xls', 'xlsx'],
                'checkExtensionByMimeType' => true,
            ],
        ];
    }

    public function import(): array|false
    {
        if (!$this->validate()) {
            return false;
        }
        return new XlsImportService()->importXlsToMongo($this->file->tempName);
    }
}
