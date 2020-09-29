<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property string|null $annotation
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_deleted
 *
 * @property AuthorBook[] $authorBooks
 * @property Author[] $authors
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
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
            [['title'], 'required'],
            [['annotation'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'annotation' => 'Annotation',
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
        return $this->hasMany(AuthorBook::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\AuthorQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('author_book', ['book_id' => 'id'])
            ->andWhere([Author::tableName().'.is_deleted' => false]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\BookQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\BookQuery(get_called_class());
    }
}
