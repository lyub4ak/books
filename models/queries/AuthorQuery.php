<?php

namespace app\models\queries;

use app\models\Author;

/**
 * This is the ActiveQuery class for [[\app\models\Author]].
 *
 * @see \app\models\Author
 */
class AuthorQuery extends \yii\db\ActiveQuery
{
    /**
     * @return self
     */
    public function notDeleted()
    {
        return $this->andWhere([Author::tableName().'.is_deleted' => false]);
    }
}
