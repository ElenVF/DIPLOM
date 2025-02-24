<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Bid $model */

$this->title = 'Create Bid';
$this->params['breadcrumbs'][] = ['label' => 'Bids', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bid-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
