<?php

use app\models\Book;
use app\models\Category;
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать книгу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
      
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'year',
            'author',
            [
                'attribute' => 'category_id',
                'value' =>function ($model){
                return Category::findOne(['id'=>$model->category_id])->name;}
            ],
            /*[
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],*/
            [
                'content' => function (Book $model) {
                    return Html::a(
                            'Посмотреть',
                            [
                                'view',
                                'id' => Html::encode($model->id)
                            ],
                            ['class' => 'btn btn-primary']
                        )
                        .
                        Html::a(
                            'Удалить',
                            ['delete', 'id' => Html::encode($model->id)],
                            ['class' => 'btn btn-danger deleteOpenModalBtn']
                        );
                }
            ],
       
        ],
    ]); ?>

    </div>
</div>
