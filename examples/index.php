<?php

use kosuha606\Minidoc\DocsBuilder;

require_once __DIR__.'/../vendor/autoload.php';

echo (new DocsBuilder())
    ->addParseParam('category')
    ->addParseParam('description')
    ->addParseParam('version')
    ->addClassRegexp('/classes/')
    ->addPreloadClassesDir(__DIR__.'/classes')
    ->buildTemplate()
;
