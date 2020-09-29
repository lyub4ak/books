<?php

namespace app\models\queries;

use app\models\Book;

/**
 * This is the ActiveQuery class for [[\app\models\Book]].
 *
 * @see \app\models\Book
 */
class BookQuery extends \yii\db\ActiveQuery
{
    /**
     * @return self
     */
    public function notDeleted()
    {
        return $this->andWhere([Book::tableName().'.is_deleted' => false]);
    }
}
