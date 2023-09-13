<?php

namespace frontend\models;

use yii\base\Model;

class TaskForm extends Model
{
    public $title;
    public $description;
    public $is_active;
    public $status;

    public function rules(): array
    {
        return [
            // username and password are both required
            [['title', 'is_active', 'status'], 'required', 'message' => 'Данное поле не может быть пустым'],
            ['description', 'safe'],
            ['title', 'string', 'length' => [1, 128]],
            // rememberMe must be a boolean value
            [['is_active', 'status'], 'integer'],
            [['description', 'title'], 'trim']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Заголовок',
            'description' => 'Описание',
            'is_active' => 'Активность',
            'status' => 'Статус выполнения'
        ];
    }
}
