<?php

use kosuha606\Minidoc\DocsBuilder;

require_once __DIR__.'/../vendor/autoload.php';

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

echo (new DocsBuilder())
    ->addParseParam('category')
    ->addParseParam('description')
    ->addParseParam('version')
    ->addClassRegexp('/classes/')
    ->addPreloadClassesDir(__DIR__.'/classes')
    ->setLanguage($lang)
    ->buildTemplate()
;
