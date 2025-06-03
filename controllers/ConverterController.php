<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;
use app\models\UploadForm;
require_once Yii::getAlias('@vendor/setasign/fpdf/fpdf.php');

class ConverterController extends Controller
{
    public function actionIndex()
    {
        $model = new UploadForm();
        return $this->render('index', ['model' => $model]);
    }

    public function actionConvert()
    {
        $model = new UploadForm();
        $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');

        if ($model->imageFiles && $model->validateImageFiles() && $model->validate()) {
            try {
                $filePaths = $model->upload();
                if ($filePaths) {
                    $pdfPath = $this->convertToPdf($filePaths);
                    
                    $this->cleanupFiles($filePaths);
                    
                    if ($pdfPath && file_exists($pdfPath)) {
                        return $this->sendFile($pdfPath);
                    } else {
                        Yii::$app->session->setFlash('error', 'Ошибка при создании PDF файла.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при загрузке файлов.');
                }
            } catch (\Exception $e) {
                Yii::error('PDF conversion error: ' . $e->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'Произошла ошибка при конвертации: ' . $e->getMessage());
            }
        } else {
            Yii::$app->session->setFlash('error', 'Пожалуйста, исправьте ошибки в форме.');
        }

        return $this->render('index', ['model' => $model]);
    }

    private function convertToPdf($imagePaths)
    {
        $pdf = new FPDF();
        
        foreach ($imagePaths as $imagePath) {
            $imageInfo = getimagesize($imagePath);
            if ($imageInfo === false) {
                continue;
            }

            $imageWidth = $imageInfo[0];
            $imageHeight = $imageInfo[1];
            
            $maxWidth = 190;
            $maxHeight = 270;
            
            $ratio = min($maxWidth / $imageWidth, $maxHeight / $imageHeight);
            $newWidth = $imageWidth * $ratio;
            $newHeight = $imageHeight * $ratio;
            
            $pdf->AddPage();
            
            $x = (210 - $newWidth) / 2;
            $y = (297 - $newHeight) / 2;
            
            $pdf->Image($imagePath, $x, $y, $newWidth, $newHeight);
        }

        $pdfPath = Yii::getAlias(Yii::$app->params['uploadPath']) . DIRECTORY_SEPARATOR . 'converted_' . uniqid() . '.pdf';
        $pdf->Output('F', $pdfPath);
        
        return $pdfPath;
    }

    private function sendFile($filePath)
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->add('Content-Type', 'application/pdf');
        $response->headers->add('Content-Disposition', 'attachment; filename="converted_images.pdf"');
        $response->headers->add('Content-Length', filesize($filePath));
        
        $response->content = file_get_contents($filePath);
        
        register_shutdown_function(function() use ($filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        });
        
        return $response;
    }

    private function cleanupFiles($filePaths)
    {
        foreach ($filePaths as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}