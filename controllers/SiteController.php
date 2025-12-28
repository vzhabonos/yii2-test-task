<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\opensearch\aggregationQueries\BadmRegionProductQuery;
use app\components\opensearch\AggregationQueryDataProvider;
use app\models\forms\BadmImportForm;
use app\models\mongo\BadmShipment as BadmShipmentMongo;
use app\models\opensearch\BadmShipment as BadmShipmentOpensearch;
use app\services\badm\MongoToOpensearchSyncService;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\mongodb\Exception;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'sync-mongo-to-opensearch' => ['POST']
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

    /**
     * @throws Exception
     */
    public function actionIndex(): string
    {
        $badmImportForm = new BadmImportForm();
        if ($badmImportForm->load(Yii::$app->request->post())) {
            $badmImportForm->file = UploadedFile::getInstance($badmImportForm, 'file');
            try {
                $result = $badmImportForm->import();
                if ($result === false) {
                    Yii::$app->session->setFlash(
                        'error',
                        Yii::t('app', 'Error while importing data from file to MongoDB.')
                    );
                } else {
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(
                            'app',
                            'Data successfully imported from file to MongoDB. Inserted rows - {insertedCount}, skipped rows - {skippedCount}.',
                            ['insertedCount' => $result['inserted'], 'skippedCount' => $result['skipped'] ?? 0]
                        )
                    );
                }
            } catch (Throwable $exception) {
                Yii::error($exception);
                Yii::$app->session->setFlash('error', Yii::t('app', 'Something went wrong.'));
            }
        }

        return $this->render('index', [
            'currentMongoRowsCount' => BadmShipmentMongo::find()->count(),
            'currentOpensearchRowsCount' => BadmShipmentOpensearch::find()->count(),
            'badmImportFormModel' => $badmImportForm,
            'aggregationDataProvider' => new AggregationQueryDataProvider(new BadmRegionProductQuery(), [
                'pagination' => ['pageSize' => 50],
                'sort' => [
                    'attributes' => ['region', 'product_name', 'quantity'],
                ],
            ])
        ]);
    }

    public function actionSyncMongoToOpensearch(): Response
    {
        try {
            $result = new MongoToOpensearchSyncService()->syncAll();
            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'app',
                    'Sync completed. Processed rows - {processedCount}.',
                    ['processedCount' => $result['processed'] ?? 0]
                )
            );
        } catch (Throwable $exception) {
            Yii::error($exception);
            Yii::$app->session->setFlash('error', Yii::t('app', 'Something went wrong.'));
        }
        return $this->redirect(['index']);
    }
}
