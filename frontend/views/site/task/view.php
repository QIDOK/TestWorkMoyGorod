<?php

/**
 * @var $this yii\web\View
 * @var $model frontend\models\TaskForm
 * @var $comments common\models\TaskComment
 */

use common\models\TaskComment;
use common\models\User;
use yii\bootstrap5\Tabs;
use yii\helpers\Html;

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="task-view">
    <div class="task-header flex-justify-between">
        <h2><?=$model->title?></h2>
        <div class="button-container">
            <?=Html::button('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>', [
                'class' => 'btn btn-primary',
                'style' => 'width: fit-content;',
                'onClick' => "document.location='/index.php?r=site%2Fupdate&id=$model->id'"
            ])?>
            <?=Html::button('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1em" height="1em"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>', [
                'class' => 'btn btn-danger',
                'style' => 'width: fit-content;',
                'onClick' => "document.location='/index.php?r=site%2Fdelete&id=$model->id'"
            ])?>
        </div>
    </div>

    <div class="task-body">
        <div class="row">
            <div class="task-description">
                <?= $model->description ?>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="task-comments">
                <div class="task-comments-header flex-justify-between">
                    <h5>Комментарии</h5>
                    <?= Html::button('Написать комментарий', [
                        'class' => 'btn btn-primary',
                        'onClick' => "document.location='/index.php?r=site/comment-create&task_id=$model->id'"
                    ])
                    ?>
                </div>
                <div class="task-comment-body">
                    <?php foreach ($comments as $comment): ?>
                        <div class="task-comment">
                            <div class="comment-header flex-justify-between">
                                <p><?=(new TaskComment())->getOwnerName($comment->owner)?></p>
                                <div class="button-container">
                                    <?=Html::button('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="0.7em" height="0.7em"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>', [
                                        'class' => 'btn',
                                        'style' => 'width: fit-content;',
                                        'onClick' => "document.location='/index.php?r=site%2Fcomment-update&id=$comment->id'"
                                    ])?>
                                    <?=Html::button('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="0.7em" height="0.7em"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>', [
                                        'class' => 'btn',
                                        'style' => 'width: fit-content;',
                                        'onClick' => "document.location='/index.php?r=site%2Fcomment-delete&id=$comment->id'"
                                    ])?>
                                </div>
                            </div>
                            <div class="comment-body">
                                <?=$comment->text?>
                            </div>
                            <div class="comment-footer">
                                <p>Изменено: <?=$comment->updated_at?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
