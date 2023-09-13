<?php

/* @var $this yii\web\View */
/* @var $model frontend\models\TaskForm */

$this->title = 'Изменение задачи';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="task-update">
    <?= $this->render('form', [
        'model' => $model,
    ]) ?>
</div>
