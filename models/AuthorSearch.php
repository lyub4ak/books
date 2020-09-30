<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Author;

/**
 * AuthorSearch represents the model behind the search form of `app\models\Author`.
 */
class AuthorSearch extends Author
{
    /**
     * Books count.
     *
     * @var int
     */
    public $booksCount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by_id', 'updated_by_id', 'created_at', 'updated_at', 'is_deleted'], 'integer'],
            [['name', 'biography', 'booksCount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Author::find()->select([
            '{{%author}}.*',
            '{{%book}}.id as bookId',
            'COUNT({{%book}}.id) AS booksCount',
        ])
            ->joinWith('books')
            ->notDeleted()
            ->groupBy('{{%author}}.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $sortAttributes = $dataProvider->getSort()->attributes;
        // adds sorting by books count
        $sortAttributes['booksCount'] = [
            'asc' => ['booksCount' => SORT_ASC],
            'desc' => ['booksCount' => SORT_DESC],
            'label' => 'Books Count'
        ];
        $dataProvider->setSort(['attributes' => $sortAttributes]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_by_id' => $this->created_by_id,
            'updated_by_id' => $this->updated_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'biography', $this->biography]);

        // filter by books count
        $query->filterHaving(['booksCount' => $this->booksCount]);

        return $dataProvider;
    }
}
