<?php

use app\models\forms\BadmImportForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\View;

/** @var View $this */
/** @var int $currentMongoRowsCount */
/** @var int $currentOpensearchRowsCount */
/** @var BadmImportForm $badmImportFormModel */
/** @var ArrayDataProvider $aggregationDataProvider */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-12 col-lg-6 d-flex align-items-stretch mb-3">
                <div class="card w-100">
                    <h5 class="card-header bg-success text-white fw-bold">Import from XLS To MongoDB</h5>
                    <div class="card-body d-flex flex-column h-100">
                        <div class="card-subtitle mb-3 fw-bold"><?= Yii::t(
                                    'app',
                                    'Current rows count in MongoDB collection: {count}',
                                    ['count' => $currentMongoRowsCount]
                            ) ?></div>
                        <?php
                        $formImport = ActiveForm::begin(['id' => 'import-xls-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $formImport
                                ->field($badmImportFormModel, 'file')
                                ->fileInput()
                                ->label(false)
                                ->hint(Yii::t('app', 'Allowed file formats: xls, xlsx')) ?>
                        <?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success mt-auto']) ?>
                        <?php
                        ActiveForm::end() ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-stretch mb-3">
                <div class="card w-100">
                    <h5 class="card-header bg-warning fw-bold">Synchronize MongoDB to Opensearch</h5>
                    <div class="card-body d-flex flex-column h-100">
                        <div class="card-subtitle mb-3 fw-bold"><?= Yii::t(
                                    'app',
                                    'Current rows count in Opensearch index: {count}',
                                    ['count' => $currentOpensearchRowsCount]
                            ) ?></div>
                        <?php
                        $formSync = ActiveForm::begin(['id' => 'sync-mongo-to-opensearch-form', 'action' => ['site/sync-mongo-to-opensearch']]); ?>
                        <?= Html::submitButton(Yii::t('app', 'Sync'), ['class' => 'btn btn-warning mt-auto']) ?>
                        <?php
                        ActiveForm::end() ?>
                    </div>
                </div>
            </div>
            <div class="col-12 d-flex align-items-stretch mb-3">
                <div class="card w-100">
                    <h5 class="card-header bg-primary fw-bold text-white">Aggregation from Opensearch</h5>
                    <div class="card-body">
                        <?= GridView::widget([
                            'dataProvider' => $aggregationDataProvider,
                            'columns' => [
                                [
                                    'attribute' => 'region',
                                    'label' => Yii::t('app', 'Region')
                                ],
                                [
                                    'attribute' => 'product_name',
                                    'label' => Yii::t('app', 'Product Name')
                                ],
                                [
                                    'attribute' => 'quantity',
                                    'label' => Yii::t('app', 'Quantity')
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
