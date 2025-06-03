<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    public $imageFiles;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 
                'skipOnEmpty' => false, 
                'extensions' => Yii::$app->params['allowedExtensions'], 
                'maxSize' => Yii::$app->params['maxFileSize'],
                'maxFiles' => 10,
                'checkExtensionByMimeType' => false,
                'message' => 'Пожалуйста, выберите файлы JPG.',
                'wrongExtension' => 'Разрешены только файлы JPG/JPEG.',
                'tooBig' => 'Размер файла не должен превышать 10MB.',
                'tooMany' => 'Можно загрузить максимум 10 файлов.'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'imageFiles' => 'JPG изображения',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $uploadPath = Yii::getAlias(Yii::$app->params['uploadPath']);
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $filePaths = [];
            foreach ($this->imageFiles as $file) {
                $fileName = uniqid() . '.' . $file->extension;
                $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;
                
                if ($file->saveAs($filePath)) {
                    $filePaths[] = $filePath;
                } else {
                    foreach ($filePaths as $savedFile) {
                        if (file_exists($savedFile)) {
                            unlink($savedFile);
                        }
                    }
                    return false;
                }
            }
            return $filePaths;
        }
        return false;
    }

    public function validateImageFiles()
    {
        if (empty($this->imageFiles)) {
            $this->addError('imageFiles', 'Пожалуйста, выберите хотя бы один файл.');
            return false;
        }

        foreach ($this->imageFiles as $file) {
            $imageInfo = getimagesize($file->tempName);
            if ($imageInfo === false) {
                $this->addError('imageFiles', 'Файл "' . $file->name . '" не является корректным изображением.');
                return false;
            }

            if (!in_array($imageInfo['mime'], ['image/jpeg', 'image/jpg'])) {
                $this->addError('imageFiles', 'Файл "' . $file->name . '" должен быть в формате JPG/JPEG.');
                return false;
            }
        }
        return true;
    }
}