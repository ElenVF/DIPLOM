<?php
/** @var yii\web\View $this */
use app\models\Event;
use yii\bootstrap5\Html;
$this->title = 'Доска объявлений';
?>
<div class="site-events">
    <div class="container">
        <h1><?php print $this->title ?></h1>
        <div class="text mb-5 col-lg-6">
            <?php
            /** @var Event[] $items */
            foreach ($items as $item) {
                ?>
                <div class="d-lg-flex site-events-item">
                    <div class="col-lg-6">
                        <div class="site-events-item-name"><?php print Html::encode($item->name) ?></div>
                        <div class="site-events-item-description"><?php print nl2br(Html::encode($item->description)) ?></div>
                    </div>
                    <div class="col-lg-6">
                        <div class="site-events-item-date">
                            <?php
                            print Yii::$app->formatter->asDate($item->date_start, 'php:d.m H:i'); //
                            print ' - ';
                            print Yii::$app->formatter->asDate($item->date_end, 'php:d.m H:i');
                            ?>
                        </div>
                        <div class="site-events-item-address"><?php print Html::encode($item->address) ?></div>
                    </div>
                </div>
                <?php
                }
                ?>
        </div>
    </div>
    <img src="/images/events-mobile.png" class="only-mobile w-100 img-fluid" alt="home mobile">
</div>
