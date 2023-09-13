<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $owner
 * @property string $title
 * @property string|null $description
 * @property int $status
 * @property int $is_active
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $owner0
 */
class Task extends \yii\db\ActiveRecord
{
    const TASK_NOT_ACTIVE = 0;
    const TASK_ACTIVE = 1;
    const TASK_ACTIVITIES = [
        self::TASK_NOT_ACTIVE => "Не активна",
        self::TASK_ACTIVE => "Активна"
    ];

    const TASK_STATUS_NEW = 0;
    const TASK_STATUS_AT_WORK = 1;
    const TASK_STATUS_TESTING = 2;
    const TASK_STATUS_FOR_REVISION = 3;
    const TASK_STATUS_COMPLETED = 4;
    const TASK_STATUS_CANCELED = 5;
    const TASK_STATUSES = [
        self::TASK_STATUS_NEW => 'Новая',
        self::TASK_STATUS_AT_WORK => 'В работе',
        self::TASK_STATUS_TESTING => 'На тестировании',
        self::TASK_STATUS_FOR_REVISION => 'На доработке',
        self::TASK_STATUS_COMPLETED => 'Завершена',
        self::TASK_STATUS_CANCELED => 'Отменена'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner', 'title'], 'required'],
            [['owner', 'status', 'is_active'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['owner'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner' => 'Создал',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'status' => 'Статус выполнения',
            'is_active' => 'Активность',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    /**
     * Gets query for [[Owner0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner0()
    {
        return $this->hasOne(User::class, ['id' => 'owner']);
    }

    public function getOwnerName($owner_id) {
        return User::findOne($owner_id)->username;
    }

    public function getStatusName($status_id) {
        return self::TASK_STATUSES[$status_id];
    }
}
