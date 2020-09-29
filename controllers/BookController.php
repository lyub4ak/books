<?php

namespace app\controllers;

use app\models\Author;
use app\models\AuthorBook;
use Yii;
use app\models\Book;
use app\models\BookSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Book models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Book();

        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            // saves authors of book
            $authors = $post['Book']['authors'];
            foreach ($authors as $author) {
                $authorBook = new AuthorBook([
                    'author_id' => $author,
                    'book_id' => $model->id
                ]);
                if(!$authorBook->save()) {
                    Yii::$app->session->setFlash('error', 'Author did not save. ' . implode(',', $authorBook->getErrorSummary(true)));
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => Author::find()->select(['id', 'name'])->notDeleted()->all(),
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $book = $this->findModel($id);

        $post = Yii::$app->request->post();
        if ($book->load($post) && $book->save()) {
            // updates authors of book
            $authors = $post['Book']['authors'];
            $authorsSaved = $book->getAuthorBooks()->indexBy('author_id')->all();

            // removes relations
            $authorsForRemove = array_diff(array_keys($authorsSaved), $authors);
            foreach ($authorsForRemove as $authorId) {
                $authorsSaved[$authorId]->delete();
            }

            // saves relations
            $authorsForSave = array_diff($authors, array_keys($authorsSaved));
            foreach ($authorsForSave as $authorId) {
                $authorBook = new AuthorBook([
                    'author_id' => $authorId,
                    'book_id' => $id
                ]);
                if(!$authorBook->save()) {
                    Yii::$app->session->setFlash('error', 'Author did not save. ' . implode(',', $authorBook->getErrorSummary(true)));
                }
            }

            return $this->redirect(['view', 'id' => $book->id]);
        }

        return $this->render('update', [
            'model' => $book,
            'authors' => Author::find()->select(['id', 'name'])->notDeleted()->all(),
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $book = $this->findModel($id);

        // deletes relations
        $authorBooks = $book->authorBooks;
        foreach ($authorBooks as $authorBook) {
            $authorBook->delete();
        }
        $book->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
