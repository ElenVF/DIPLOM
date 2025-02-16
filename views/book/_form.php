<?php

use app\models\Book;
use app\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="book-form col-lg-8">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-lg-7">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => true]) ?>
            <?= $form->field($model, 'author')->textInput(['maxlength' => true, 'placeholder' => true]) ?>
            <?= $form->field($model, 'year')->textInput(['maxlength' => true, 'placeholder' => true]) ?>
            <?= $form->field($model, 'category_id')->dropDownList(Category::options()) ?>
            <?php
            if ($model->id && Yii::$app->user->identity->isAdmin) {
                print $form->field($model, 'status')->dropDownList(Book::statusOptions());
                ?>
                <div class="form-hint">Для того, чтобы книга выводилась на сайте, необходимо изменить статус на “одобрена”</div>
            <?php
            } ?>
            <?php if ($model->id && $model->getPreview()) { ?>
                <div class="book-image" style="max-width: 300px;">
                    <div class="bold-border">
                        <?= Html::img('@web/'.Html::encode($model->getPreview()), ['class' => 'img-fluid']) ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-lg-5">
            <?= $form->field($model, 'imageFile')->fileInput() ?>
            <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group right mt-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
