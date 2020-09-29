<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%author_book}}`.
 */
class m200929_092724_create_author_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%author_book}}', [
            'author_id' => $this->integer()->notNull(),
            'book_id' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk-author_book', 'author_book', ['author_id', 'book_id']);

        $this->createIndex(
            'idx-author_book-author_id',
            '{{%author_book}}',
            'author_id'
        );
        $this->addForeignKey(
            'fk-author_book-author_id',
            '{{%author_book}}',
            'author_id',
            '{{%author}}',
            'id'
        );

        $this->createIndex(
            'idx-author_book-book_id',
            '{{%author_book}}',
            'book_id'
        );
        $this->addForeignKey(
            'fk-author_book-book_id',
            '{{%author_book}}',
            'book_id',
            '{{%book}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-author_book-book_id',
            '{{%author_book}}'
        );
        $this->dropIndex(
            'idx-author_book-book_id',
            '{{%author_book}}'
        );

        $this->dropForeignKey(
            'fk-author_book-author_id',
            '{{%author_book}}'
        );
        $this->dropIndex(
            'idx-author_book-author_id',
            '{{%author_book}}'
        );

        $this->dropPrimaryKey('pk-author_book', '{{%author_book}}');

        $this->dropTable('{{%author_book}}');
    }
}
