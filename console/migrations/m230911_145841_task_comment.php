<?php

use yii\db\Migration;

/**
 * Class m230911_145841_task_comment
 */
class m230911_145841_task_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('task_comment', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer(11)->notNull(),
            'owner' => $this->integer(11)->notNull(),
            'text' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'task_id_to_task_comment_task_id',
            'task_comment',
            'task_id',
            'task',
            'id'
        );

        $this->addForeignKey(
            'user_id_to_task_comment_owner',
            'task_comment',
            'owner',
            'user',
            'id'
        );

        Yii::$app->runAction("gii/model", ["tableName"=>"task_comment", "modelClass"=>"TaskComment", "ns"=>"\\common\\models"]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('task_id_to_task_comment_task_id', 'task_comment');
        $this->dropForeignKey('user_id_to_task_comment_owner', 'task_comment');
        $this->dropTable('task_comment');
    }
}
