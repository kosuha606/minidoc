Minidoc
---

Инструмент для быстрого сбора документации из аннтоаций
классов в проекте.

Пример вывода документации:
```php
<?php
$docsBuilder = new DocsBuilder('/contexts/');
$docsBuilder->setParseParams(['category', 'description']);
$docsBuilder->setPreloadDirClasses(__DIR__.'/../../../../contexts');
return $docsBuilder->buildTemplate();
```