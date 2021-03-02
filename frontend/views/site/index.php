<?php

/* @var $this yii\web\View */
/** @var \yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap4\LinkPager;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <?php echo \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{summary}<div class="row">{items}</div>{pager}',
                'itemView' => '_product_item',
                'itemOptions' => [
                    'class' => 'col-lg-4 col-md-6 mb-4 product-item'
                ],
                'pager' => [
                    'class' => LinkPager::class
                ]
            ])?>
            
        </div>

    </div>
</div>
