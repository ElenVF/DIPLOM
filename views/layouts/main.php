<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100 justify-content-between">
<?php $this->beginBody() ?>

<header class="header fixed-top">
    <div class="container d-lg-flex align-items-center justify-content-between">
        <div class="header-left d-lg-flex align-items-center">
            <div class="header-logo">
                <a href="<?=Yii::$app->homeUrl ?>"><img src="/images/logo.svg" alt="logo"></a>
            </div>

            <div class="header-search-btn only-mobile" id="header-search-btn"></div>
            <div class="header-search" id="header-search">
                <form method="get" action="/search" class="d-flex align-items-center">
                    <input type="text" name="search" value="<?=Yii::$app->request->get('search') ?>"
                           class="form-control" placeholder="поиск нужной книги">
                    <button type="submit" class="btn btn-primary">ПОИСК</button>
                </form>
            </div>
        </div>

        <?php
        NavBar::begin([
    //        'brandLabel' => Yii::$app->name,
    //        'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md']
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                 ['label' => 'Главная', 'url' => ['/']],
                 ['label' => 'Книги', 'url' => ['/books']],
                 ['label' => 'Доска объявлений', 'url' => ['/events']],

                !Yii::$app->user->isGuest
                    ? ['label' => 'Личный кабинет', 'url' => ['/account']]
                    : '',

//                 ['label' => 'О нас', 'url' => ['/site/about']],

                /*!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin
                ? ['label' => 'Админ панель', 'url' => ['/admin-panel']]
                : '',*/
                Yii::$app->user->isGuest
                    ? ['label' => 'Авторизация', 'url' => ['/site/login'], 'linkOptions' => ['class' => 'nav-link nav-link--btn']]
                    : '<li class="nav-item">'
                        . Html::beginForm(['/site/logout'])
                        . Html::submitButton(
                            'Выход (' . Yii::$app->user->identity->login . ')',
                            ['class' => 'nav-link nav-link--btn btn-link']
                        )
                        . Html::endForm()
                        . '</li>',

                Yii::$app->user->isGuest
                    ? ['label' => 'Регистрация', 'url' => ['/site/registration'], 'linkOptions' => ['class' => 'nav-link nav-link--btn']]
                    : '',
            ]
        ]);
        NavBar::end();
    ?>
    </div>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <?php /* if (!empty($this->params['breadcrumbs'])): ?>
        <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
    <?php endif */ ?>
    <?= Alert::widget() ?>
    <?= $content ?>
</main>

<footer id="footer">
    <img src="/images/footer.png" alt="/images/footer.png" class="w-100 only-desktop">
    <img src="/images/footer-mobile.png" alt="/images/footer-mobile.png" class="w-100 only-mobile">
    <div class="container position-relative">
        <div class="footer-content">
            <div class="d-flex">
                <div>
                    <a href="tel:+7-955-555-55-55" class="footer-phone">+7-955-555-55-55</a><br>
                    <a href="tel:+7-955-555-55-57" class="footer-phone">+7-955-555-55-57</a>
                    <div class="d-flex footer-nets">
                        <div>подпишись на нас</div>
                        <a href="https://web.telegram.org/a/" class="footer-net footer-net--tg"></a>
                        <a href="https://www.instagram.com/" class="footer-net footer-net--inst"></a>
                        <a href="https://vk.com/" class="footer-net footer-net--vk"></a>
                    </div>
                </div>
                <div class="footer-heart"></div>
            </div>
<!--            <div class="col-md-6 text-center text-md-start">&copy; My Company --><?//= date('Y') ?><!--</div>-->
<!--            <div class="col-md-6 text-center text-md-end">--><?//= Yii::powered() ?><!--</div>-->
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
