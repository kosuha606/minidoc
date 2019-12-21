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

![alt text](http://kosuha606.ru/uploads/example.png)

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
