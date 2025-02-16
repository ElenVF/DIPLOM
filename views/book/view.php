<?php

use app\models\Category;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/account']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container">
<div class="bid-view">
    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить книгу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            'year',
            'author',
           
            [                      // the owner name of the model
                'attribute' => 'category_id',
                'value' =>Category::findOne(['id'=>$model->category_id])->name,
            ],

            [                      // the owner name of the model
                'label' => 'Картинка',
                'format'=>'html',
                'value' =>Html::img('@web/'.Html::encode($model->getPreview()),['class'=>'img-fluid','width'=>'400']),
            ],
        ],
    ]) ?>

</div>
</div>
