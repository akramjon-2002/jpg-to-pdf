<?php

use yii\helpers\Html;

$this->title = 'Ошибка';
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($exception->getMessage())) ?>
    </div>

    <p>
        Произошла ошибка при обработке вашего запроса.
    </p>
    <p>
        Пожалуйста, свяжитесь с нами, если считаете, что это ошибка сервера. Спасибо.
    </p>
</div>