<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_login_api".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $token
 * @property string $created_at
 * @property string $expire_at
 *
 * @property User $user
 */
class UserLoginApi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_login_api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'expire_at'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'expire_at'], 'safe'],
            [['token'], 'string', 'max' => 39],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'created_at' => 'Created At',
            'expire_at' => 'Expire At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
