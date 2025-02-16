<?php

use app\models\Category;
use app\models\Status;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Bid $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bids', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bid-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
       
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
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
            'created_at',
            'comment',
           
            [                      // the owner name of the model
                'attribute' => 'category_id',
                'value' =>Category::findOne(['id'=>$model->category_id])->name,
            ],

            [                      // the owner name of the model
                'attribute' => 'status_id',
                'value' =>Status::findOne(['id'=>$model->status_id])->name,
            ],
            [                      // the owner name of the model
                'label' => 'Картинка',
                'format'=>'html',
                'value' =>Html::img('@web/'.Html::encode($model->getPreview()),['class'=>'img-fluid','width'=>'400']),
            ],
            [                      // the owner name of the model
                'label' => 'Картинка от Админки',
                'format'=>'html',
                'value' =>Html::img('@web/'.Html::encode($model->getPreviewAdmin()),['class'=>'img-fluid','width'=>'400']),
            ],
        ],
    ]) ?>

</div>
