<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap5\Alert;

$this->title = 'JPG to PDF Converter';
?>

<div class="converter-index">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>
                        
                        <?php if (Yii::$app->session->hasFlash('error')): ?>
                            <?= Alert::widget([
                                'body' => Yii::$app->session->getFlash('error'),
                                'options' => ['class' => 'alert-danger'],
                            ]) ?>
                        <?php endif; ?>

                        <?php if (Yii::$app->session->hasFlash('success')): ?>
                            <?= Alert::widget([
                                'body' => Yii::$app->session->getFlash('success'),
                                'options' => ['class' => 'alert-success'],
                            ]) ?>
                        <?php endif; ?>

                        <?php $form = ActiveForm::begin([
                            'id' => 'upload-form',
                            'options' => [
                                'enctype' => 'multipart/form-data',
                                'class' => 'needs-validation',
                                'novalidate' => true
                            ],
                            'action' => ['convert'],
                        ]); ?>

                        <div class="mb-4">
                            <?= $form->field($model, 'imageFiles[]')->fileInput([
                                'multiple' => true,
                                'accept' => '.jpg,.jpeg',
                                'class' => 'form-control',
                                'id' => 'file-input'
                            ])->label('Выберите JPG изображения (до 10 файлов, максимум 10MB каждый)') ?>
                        </div>

                        <div class="d-grid">
                            <?= Html::submitButton('Конвертировать в PDF', [
                                'class' => 'btn btn-primary btn-lg',
                                'id' => 'convert-btn'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                        <div id="loading-spinner" class="text-center mt-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <p class="mt-2">Конвертация в процессе...</p>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                <strong>Поддерживаемые форматы:</strong> JPG, JPEG<br>
                                <strong>Максимальный размер файла:</strong> 10MB<br>
                                <strong>Максимальное количество файлов:</strong> 10
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('#upload-form').on('submit', function() {
        var fileInput = $('#file-input')[0];
        if (fileInput.files.length === 0) {
            alert('Пожалуйста, выберите хотя бы один файл.');
            return false;
        }
        
        $('#convert-btn').prop('disabled', true);
        $('#loading-spinner').show();
        return true;
    });

    $('#file-input').on('change', function() {
        var files = this.files;
        var validFiles = 0;
        var maxSize = 10 * 1024 * 1024; // 10MB
        
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var ext = file.name.split('.').pop().toLowerCase();
            
            if (ext === 'jpg' || ext === 'jpeg') {
                if (file.size <= maxSize) {
                    validFiles++;
                } else {
                    alert('Файл \"' + file.name + '\" превышает максимальный размер 10MB.');
                    this.value = '';
                    return;
                }
            } else {
                alert('Файл \"' + file.name + '\" не является JPG изображением.');
                this.value = '';
                return;
            }
        }
        
        if (validFiles > 10) {
            alert('Можно выбрать максимум 10 файлов.');
            this.value = '';
            return;
        }
    });
");
?>