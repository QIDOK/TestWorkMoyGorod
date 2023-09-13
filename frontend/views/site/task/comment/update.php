<?php

/* @var $this yii\web\View */
/* @var $model frontend\models\TaskCommentForm */

$this->title = "Комментарий к задаче №$model->task_id";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="comment-update">
    <?= $this->render('form', [
        'model' => $model,
    ]) ?>
</div>
