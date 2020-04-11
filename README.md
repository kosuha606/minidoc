Minidoc
---

### Установка
```bash
$ composer require --dev kosuha606/minidoc
```

### Quick Start

Инструмент для быстрого сбора документации из аннтоаций
классов в проекте.

Пример вывода документации:
```php
<?php
echo (new DocsBuilder())
    ->addParseParam('category')
    ->addParseParam('description')
    ->addParseParam('version')
    ->addClassRegexp('/classes/')
    ->addPreloadClassesDir(__DIR__.'/classes')
    ->buildTemplate()
;
```

### Пример работы

![alt text](http://kosuha606.ru/uploads/common/5e8d90965a37d.png)

### Настройка
Добавить стили или скритпы в шаблон:
```php
$docsBuilder->addStyle(new ResourceDTO(__DIR__.'/resources/style.css', ResourceDTO::TYPE_FILE));
$docsBuilder->addScript(new ResourceDTO(__DIR__.'/resources/script.js', ResourceDTO::TYPE_FILE));
```
Отрендерить свой шаблон документации:
```php
$docsBuilder->setViewTemplate(__DIR__.'/views/main.php');
```
Добавтиь свои параметры для парсинга в аннотациях:
```php
$docsBuilder->setParseParams(['category', 'description']);
$docsBuilder->addParseParam('mark');
```
Настроить предзагрузку классов путем передачи пути к директории с классами:
```php
$docsBuilder->addPreloadClassesDir(__DIR__.'/../../../../contexts');
```
Обработать данные после их построения
```php
$docBuilder->afterBuildData(function() {});
```
Установить регулярное выражение для выбора файлов из указанной директории
```php
$docBuilder->setFilesRegexp();
```

Выбрать язык вывода или указать свой языковой файл
```php
$docBuilder->setLanguage('ru');
// Или указать свой массив переводов
$docBuilder->setTranslations(require_once __DIR__.'/ch.php');
```

### Change log
#### 1.0.9 (2020-04-11)
- Добавлена интернационализация
- Добавлено кэширование и возможность сброса кэша 