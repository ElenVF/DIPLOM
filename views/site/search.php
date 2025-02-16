<?php

/** @var yii\web\View $this */


use app\models\Book;

use yii\bootstrap5\Html;
$this->title = 'Поиск';
?>
<div class="search">
    <div class="container">
        <h1><?php print $this->title ?></h1>

        <div class="text mb-5 col-lg-6">
            <?php
            /** @var Book[] $items */
            if ($items) {
                foreach ($items as $item) {
                    ?>
                    <div class="d-flex search-item">
                        <div class="search-item-img">
                            <a href="/books#/<?php print Html::encode($item->id) ?>">
                                <div class="thin-border">
                                    <?= Html::img('@web/'.Html::encode($item->getPreview()), ['class' => 'img-fluid']) ?>
                                </div>
                            </a>
                        </div>
                        <div class="search-item-data">
                            <div class="search-item-name">
                                <a href="/books#/<?php print Html::encode($item->id) ?>">
                                    <?php print Html::encode($item->name) ?>
                                </a>
                            </div>
                            <div class="search-item-author"><?php print Html::encode($item->author) ?></div>
                            <div class="search-item-category"><?php print Html::encode($item->category->name) ?></div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                print 'ничего не найдено';
            }
            ?>
        </div>
    </div>
</div>
