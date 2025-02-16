<?php

use app\models\Bid;
use yii\helpers\Html;
/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">
<?php if($items){?>
<div class="card-group">
<?php foreach($items as $model){?>
  <div class="card">
   
    <?= Html::img('@web/'.Html::encode($model->preview()),['class'=>'card-img-top']) ?>
    <div class="card-body">
      <h5 class="card-title"><?= Html::encode($model->name) ?></h5>
      <p class="card-text"><?= Html::encode(Bid::findOne($model->status_id)->name) ?></p>
      <p class="card-text"><small class="text-body-secondary"><?= Html::encode($model->created_at) ?></small></p>
    </div>
  </div>
  <?php } ?>
  </div>
  <?php } ?>
</div>
