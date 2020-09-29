<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "author".
 *
 * @property int $id
 * @property string $name
 * @property string|null $biography
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_deleted
 *
 * @property AuthorBook[] $authorBooks
 * @property Book[] $books
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author';
    }

    public function behaviors()
    {
        return [
            'blameableBehavior' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by_id',
                'updatedByAttribute' => 'updated_by_id',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true,
                ],
                'replaceRegularDelete' => true,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['biography'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'biography' => 'Biography',
            'created_by_id' => 'Created By ID',
            'updated_by_id' => 'Updated By ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[AuthorBooks]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\AuthorBookQuery
     */
    public function getAuthorBooks()
    {
        return $this->hasMany(AuthorBook::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\BookQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('author_book', ['author_id' => 'id'])
            ->andWhere([Book::tableName().'.is_deleted' => false]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\AuthorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\AuthorQuery(get_called_class());
    }
}
