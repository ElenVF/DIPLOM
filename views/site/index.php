<?php

/** @var yii\web\View $this */


use app\models\Category;

use yii\bootstrap5\Html;
$this->title = 'Книжный своп - главная';
?>
<div class="home">
    <div class="container">
        <h1>Приветствуем вас на BookExchange</h1>

        <div class="text mb-5 col-lg-6">
            На уникальном сервисе книжного свопа! Мы предлагаем вам возможность обменяться книгами с другими любителями чтения, чтобы расширить свой личный кругозор и познакомиться с новыми авторами. Наша платформа позволяет создавать личные библиотеки, добавлять книги, которые вы готовы обменять, и находить интересные вам произведения в библиотеках других пользователей. Мы уверены, что BookExchange станет вашим надежным помощником в поиске новых литературных открытий и поможет вам сэкономить на покупке книг. Присоединяйтесь к нашему сообществу уже сегодня и начните обмениваться книгами!
        </div>

        <a href="/books" class="btn mt-lg-5 mb-5 btn-secondary">ОТКРОЙ ДЛЯ СЕБЯ МИР КНИЖНОГО СВОПА</a>
        <div class="circles only-desktop">
            <div class="container position-relative">
                <div class="circle"><img class="w-100" src="/images/circle.svg" alt="circle"></div>
                <div class="circle circle--mid"><img class="w-100" src="/images/circle.svg" alt="circle"></div>
                <div class="circle circle--small"><img class="w-100" src="/images/circle.svg" alt="circle"></div>
            </div>
        </div>
    </div>
    <img src="/images/home-mobile.png" class="only-mobile w-100 img-fluid" alt="home mobile">
<?php /* if($items) {?>
    <div class="body-content">
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach($items as $model){?>
  <div class="col">

    <div class="card border-primary mb-3" style="max-width: 18rem;">
    <?= Html::img('@web/'.Html::encode($model->getPreview()),['class'=>'card-img-top']) ?>

  <div class="card-header"><?= Html::encode($model->name) ?></div>
  <div class="card-body text-primary">
    <h5 class="card-title"><?= Html::encode($model->created_at) ?></h5>
    <p class="card-text"><?= Html::encode(Category::findOne(['id'=>$model->category_id])->name)  ?></p>
  </div>
</div>
</div>
<?php } ?>
        </div>

    </div>
    </div>
  </div>
    <?php } */ ?>
</div>
