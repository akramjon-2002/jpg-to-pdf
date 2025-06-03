# JPG to PDF Converter

Простое веб-приложение на PHP с использованием фреймворка Yii2 для конвертации JPG изображений в PDF файлы.

## Возможности

- Загрузка одного или нескольких JPG изображений
- Конвертация изображений в единый PDF файл
- Автоматическое масштабирование с сохранением пропорций
- Валидация файлов (тип, размер до 10MB)
- Адаптивный дизайн с Bootstrap 5
- CSRF защита
- Логирование ошибок

## Требования

- PHP 7.4 или выше
- Composer
- Веб-сервер (Apache/Nginx)
- Расширения PHP: gd, fileinfo

## Установка

1. Клонируйте репозиторий:
```bash
git clone <repository-url>
cd jpg-to-pdf
```

2. Установите зависимости через Composer:
```bash
composer install
```

3. Настройте права доступа:
```bash
chmod 755 runtime uploads web/assets
chmod 777 runtime/logs uploads
```

4. Настройте веб-сервер:

### Apache
Убедитесь, что DocumentRoot указывает на папку `web/` и включен mod_rewrite.

### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/jpg-to-pdf/web;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Встроенный сервер PHP (для разработки)
```bash
cd web
php -S localhost:8000
```

## Использование

1. Откройте приложение в браузере
2. Выберите один или несколько JPG файлов (до 10 файлов, максимум 10MB каждый)
3. Нажмите "Конвертировать в PDF"
4. PDF файл автоматически загрузится

## Структура проекта

```
jpg-to-pdf/
├── assets/              # Asset bundles
├── config/              # Конфигурационные файлы
├── controllers/         # Контроллеры
├── models/             # Модели
├── runtime/            # Временные файлы и логи
├── uploads/            # Загруженные файлы (временно)
├── views/              # Представления
├── web/                # Веб-корень
├── composer.json       # Зависимости Composer
└── README.md          # Этот файл
```

## Конфигурация

### Основные параметры (config/web.php)

- `maxFileSize` - максимальный размер файла (по умолчанию 10MB)
- `allowedExtensions` - разрешенные расширения файлов
- `uploadPath` - путь для временных файлов

### Безопасность

- CSRF защита включена по умолчанию
- Валидация типов файлов на стороне сервера
- Автоматическая очистка временных файлов
- Логирование ошибок

## Разработка

### Включение режима отладки

В файле `web/index.php` измените:
```php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
```

### Логи

Логи ошибок сохраняются в `runtime/logs/app.log`

## Технические детали

- **Фреймворк**: Yii2
- **PDF библиотека**: setasign/fpdf + setasign/fpdi
- **CSS фреймворк**: Bootstrap 5
- **Стандарты кода**: PSR-12

## Лицензия

MIT License