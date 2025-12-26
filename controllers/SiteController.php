<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\ImportXlsToMongoDbForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ]
        ];
    }

    public function actionIndex(): string
    {
        $importXlsFormModel = new ImportXlsToMongoDbForm();
        if ($importXlsFormModel->load(Yii::$app->request->post()) && $importXlsFormModel->validate()) {
            $result = $importXlsFormModel->import();
            if ($result === false) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Error while importing data to MongoDB.'));
            } else {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'Data imported successfully to MongoDB. Imported rows - {importedCount}, skipped rows - {skippedCount}.',
                        ['importedCount' => $result['imported'], 'skippedCount' => $result['skipped'] ?? 0]
                    )
                );
            }
        }
        return $this->render('index', [
            'importXlsFormModel' => $importXlsFormModel,
        ]);
    }
}
