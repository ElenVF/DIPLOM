<?php

/** @var yii\web\View $this */
/** @var \yii\db\ActiveRecord[] $items */

$this->registerJsFile(
    '@web/js/books.js',
    ['depends' => [yii\web\YiiAsset::class]]
);

$this->title = 'Доступные книги';
?>
<div class="books">
    <router-view></router-view>
</div>