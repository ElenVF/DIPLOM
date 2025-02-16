<?php

use app\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Bid $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="bid-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_start')->textInput(['type'=>'datetime-local','min'=>date('Y-m-d').'T08:00']) ?>

    <?= $form->field($model, 'date_end')->textInput(['type'=>'datetime-local','min'=>date('Y-m-d').'T08:00']) ?>


   

    <?= $form->field($model, 'category_id')->dropDownList(Category::options()) ?>
    
    <?= $form->field($model, 'imageFile')->fileInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
