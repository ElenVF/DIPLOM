<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registration">
    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-lg-8">

                <?php $form = ActiveForm::begin([
                    'id' => 'enableAjaxValidation',
                    /*'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                        'horizontalCssClasses' => [
                            'offset' => 'col-lg-10 offset-lg-2',
                            'label' => 'col-lg-2 col-form-label',
                            'wrapper' => 'col-lg-10',
                            'error' => '',
                            'hint' => '',
                            'field' => 'mb-4 row align-items-start',
                        ],
                    ],*/
                ]); ?>

                <?= $form->field($model, 'login',['enableAjaxValidation'=>true])->textInput(['autofocus' => false, 'placeholder' => true]) ?>
                <?= $form->field($model, 'name')->textInput(['placeholder' => true]) ?>
                <?= $form->field($model, 'email',['enableAjaxValidation'=>true])->textInput(['placeholder' => true]) ?>
                <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '+7(999)999-99-99', 'options' => ['placeholder' => true]
    ]) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => true]) ?>
                <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => true]) ?>
                <?= $form->field($model, 'terms')->checkbox(['placeholder' => true]) ?>

                <div class="form-hint">Используя данный сайт, вы даете согласие на использование файлов cookie, помогающих нам сделать его удобнее для вас.</div>
                <div class="form-group right">
                    <div>
                        <?= Html::submitButton('зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
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
        <img src="/images/registration-mobile.png" class="only-mobile w-100 img-fluid" alt="home mobile">
    </div>
</div>
