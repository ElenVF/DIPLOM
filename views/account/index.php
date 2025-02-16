<?php

use app\models\Bid;
use app\models\Category;
use app\models\Status;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BidSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Заявки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать заявку', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
      
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'description',
            'created_at',
            'comment',
            //'status_id',
            //'user_id',
            //'category_id',
            [                      // the owner name of the model
                'attribute' => 'category_id',
                'value' =>function ($model){
                return Category::findOne(['id'=>$model->category_id])->name;}
            ],
            [                      // the owner name of the model
                'attribute' => 'status_id',
                'value' =>function ($model){
                return Status::findOne(['id'=>$model->status_id])->name;}
            ],

            [                      // the owner name of the model
          
                'content' =>function ($model){
                return Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]).
                Html::a('Просмотр', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ;
            
            
            
            
            
            }
            ],

       
        ],
    ]); ?>


</div>
