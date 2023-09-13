<?php

use yii\db\Migration;

/**
 * Class m230912_140608_user_login_api
 */
class m230912_140608_user_login_api extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_login_api', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'token' => $this->string(39),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'expire_at' => $this->timestamp()->notNull()
        ]);

        $this->addForeignKey(
            'user_id_to_user_login_api_user_id',
            'user_login_api',
            'user_id',
            'user',
            'id'
        );

        Yii::$app->runAction("gii/model", ["tableName"=>"user_login_api", "modelClass"=>"UserLoginApi", "ns"=>"\\common\\models"]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("user_id_to_user_login_api_user_id", "user_login_api");
        $this->dropTable("user_login_api");
    }
}
