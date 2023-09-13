<?php

use common\models\Task;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $model frontend\models\TaskForm
 */
?>

<?php $form = ActiveForm::begin(['id' => 'form-task']); ?>

<div class="task-form">
    <div class="fields">
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'title') ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'description')->textarea() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'status')->dropDownList(TASK::TASK_STATUSES) ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'is_active')->dropDownList(Task::TASK_ACTIVITIES) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::button('Назад', [
           'class' => 'btn btn-danger',
           'onClick' => 'window.history.back()'
        ]) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
