<?php

use app\models\Bid;
use app\models\Category;
use app\models\Status;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */

$this->registerJsFile(
    '@web/js/account.js',
    ['depends' => [yii\web\YiiAsset::class]]
);

$this->title = 'Личный кабинет';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account mb-5">
    <div class="container">
        <h1>Личный кабинет</h1>
        <div class="row">
            <div class="col-lg-2">
                <div class="account-user">
                    <ul class="account-user-data">
                        <li><?=Yii::$app->user->identity->name ?></li>
                        <li><?=Yii::$app->user->identity->email ?></li>
                        <li><?=Yii::$app->user->identity->phone ?></li>
                        <li><router-link :to="{ name: 'books'}" class="button">Мои книги</router-link></li>
                        <li><router-link :to="{ name: 'bids'}" class="button">Мои заявки <span class="counter-circle">{{ bidCounter }}</span></router-link></li>
                        <li><a href="<?=Url::to(['book/create']) ?>">Добавить книгу</a></li>
                        <li><router-link :to="{ name: 'profile'}" class="button">Редактировать профиль</router-link></li>
                        <?php
                        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) {
                        ?>
                            <li><router-link :to="{ name: 'books-all'}" class="button">Все книги <span class="counter-circle">{{ bookCounter }}</span></router-link></li>
                            <li><router-link :to="{ name: 'crud', params: {model: 'categories'}}" class="button">Жанры</router-link></li>
                            <li><router-link :to="{ name: 'crud', params: {model: 'delivery'}}" class="button">Способы доставки</router-link></li>
                            <li><router-link :to="{ name: 'events'}" class="button">Объявления</router-link></li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-8 col-xxl-6">
                <router-view @get-counters="getCounters"></router-view>
            </div>
        </div>
    </div>
    <img src="/images/account-mobile.png" class="only-mobile w-100 img-fluid" alt="home mobile">
    <div class="circles only-desktop">
        <div class="container position-relative">
<!--            <div class="circle circle1"><img class="w-100" src="/images/circle.svg" alt="circle"></div>-->
<!--            <div class="circle circle--mid"><img class="w-100" src="/images/circle.svg" alt="circle"></div>-->
<!--            <div class="circle circle--small"><img class="w-100" src="/images/circle.svg" alt="circle"></div>-->
<!--            <div class="circle circle--small circle--small2"><img class="w-100" src="/images/circle.svg" alt="circle"></div>-->
        </div>
    </div>
</div>