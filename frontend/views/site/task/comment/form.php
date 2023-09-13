<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $model frontend\models\TaskCommentForm
 */
?>

<?php $form = ActiveForm::begin(['id' => 'form-task']); ?>

<div class="task-form">
    <div class="fields">
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'text') ?>
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
