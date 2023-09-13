<?php

/* @var $this yii\web\View */
/* @var $model frontend\models\TaskForm */

$this->title = 'Создание задачи';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="task-create">
    <?= $this->render('form', [
        'model' => $model,
    ]) ?>
</div>
