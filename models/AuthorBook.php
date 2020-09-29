<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "author_book".
 *
 * @property int $author_id
 * @property int $book_id
 *
 * @property Author $author
 * @property Book $book
 */
class AuthorBook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'book_id'], 'required'],
            [['author_id', 'book_id'], 'integer'],
            [['author_id', 'book_id'], 'unique', 'targetAttribute' => ['author_id', 'book_id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'author_id' => 'Author ID',
            'book_id' => 'Book ID',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\AuthorQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\BookQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\AuthorBookQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\AuthorBookQuery(get_called_class());
    }
}
