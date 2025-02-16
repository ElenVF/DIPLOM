<?php

use yii\helpers\Html;
use yii\bootstrap5\Breadcrumbs;

/** @var yii\web\View $this */
/** @var app\models\Bid $model */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/account']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="book-update">
    <div class="container">
        <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
