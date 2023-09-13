<?php

/** @var yii\web\View $this */

use common\models\Task;
use yii\bootstrap5\Tabs;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;use yii\helpers\Html;

$this->title = 'Задачи';


?>
<div class="task-index">
    <?=
        Tabs::widget([
            'items' => [
                [
                    'label' => 'Активные',
                    'content' =>
                        GridView::widget([
                        'dataProvider' => new ActiveDataProvider([
                            'query' => Task::find()->where([
                                'owner' => Yii::$app->user->id,
                                'is_active' => Task::TASK_ACTIVE
                            ])
                        ]),
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'format'    => 'text',
                                'label'     => Yii::t('app', 'ID'),
                            ],
                            [
                                'attribute' => 'title',
                                'format'    => 'text',
                            ],
                            [
                                'attribute' => 'owner',
                                'format'    => 'text',
                                'value'     => function (Task $model) {
                                    return $model->getOwnerName($model->owner);
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'text',
                                'value' => function (Task $model) {
                                    return $model->getStatusName($model->status);
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'format'    => 'text',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format'    => 'text',
                            ],
                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]),
                    'active' => true
                ],
                [
                    'label' => 'Отключённые',
                    'content' =>
                        GridView::widget([
                        'dataProvider' => new ActiveDataProvider([
                            'query' => Task::find()->where([
                                'owner' => Yii::$app->user->id,
                                'is_active' => Task::TASK_NOT_ACTIVE
                            ])
                        ]),
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'format'    => 'text',
                                'label'     => Yii::t('app', 'ID'),
                            ],
                            [
                                'attribute' => 'title',
                                'format'    => 'text',
                            ],
                            [
                                'attribute' => 'owner',
                                'format'    => 'text',
                                'value'     => function (Task $model) {
                                    return $model->getOwnerName($model->owner);
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'text',
                                'value' => function (Task $model) {
                                    return $model->getStatusName($model->status);
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'format'    => 'text',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format'    => 'text',
                            ],
                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                ]),
                    'options' => ['id' => 'myveryownID'],
                ],
            ],
        ]);
    ?>

    <?=
        Html::button('Создать задачу', [
            'class' => 'btn btn-primary',
            'onclick' => 'document.location=\'index.php?r=site/create\''
        ])
    ?>
</div>
