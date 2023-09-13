<?php

use yii\db\Migration;

/**
 * Class m230910_180759_task
 */
class m230910_180759_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('task', [
            'id' => $this->primaryKey(),
            'owner' => $this->integer(11)->notNull(),
            'title' => $this->string(128)->notNull(),
            'description' => $this->text(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'user_id_to_task_owner',
            'task',
            'owner',
            'user',
            'id'
        );

        Yii::$app->runAction("gii/model", ["tableName"=>"task", "modelClass"=>"Task", "ns"=>"\\common\\models"]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user_id_to_task_owner', 'task');
        $this->dropTable('task');
    }
}
