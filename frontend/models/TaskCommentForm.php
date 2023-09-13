<?php

namespace frontend\models;

use yii\base\Model;

class TaskCommentForm extends Model
{
    public $text;
    public $task_id;

    public function rules()
    {
        return [
            ['text', 'required', 'message' => 'Данное поле не может быть пустым'],
            ['text', 'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'text' => 'Комментарий'
        ];
    }
}
