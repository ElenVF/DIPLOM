<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    /*'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                        'inputOptions' => ['class' => 'col-lg-3 form-control'],
                        'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                    ],*/
                ]); ?>

                <?= $form->field($model, 'login')->textInput(['autofocus' => false, 'placeholder' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput(['placeholder' => true]) ?>

                <?= $form->field($model, 'rememberMe')->checkbox([
//                    'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
                ]) ?>

                <div class="form-group right">
                    <div>
                        <?= Html::submitButton('Авторизоваться', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>



            </div>
        </div>

<!--        <div class="circles only-desktop">
            <div class="container position-relative">
                <div class="circle"><img class="w-100" src="/images/circle.svg" alt="circle"></div>
                <div class="circle circle&#45;&#45;mid"><img class="w-100" src="/images/circle.svg" alt="circle"></div>
                <div class="circle circle&#45;&#45;small"><img class="w-100" src="/images/circle.svg" alt="circle"></div>
            </div>
        </div>-->
    </div>
    <img src="/images/login-mobile.png" class="only-mobile w-100 img-fluid mt-5" alt="home mobile">
</div>
