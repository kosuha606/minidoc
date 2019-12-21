<?php

/** @var $stylesInline */
/** @var $classesData */
/** @var $stylesUrl */
/** @var $scriptsUrl */
/** @var $scriptsInline */


foreach ($classesData as $classesDatum) {
    if ($classesDatum['category']) {
        $categories[$classesDatum['category']][] = $classesDatum;
    } else {
        $categories['Без_категории'][] = $classesDatum;
    }
}

$nocategory = isset($categories['Без_категории']) ? $categories['Без_категории'] : [];
unset($categories['Без_категории']);
$nocategoryLabel = 'Без_категории';

$k = 1;


?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php foreach ($stylesUrl as $url) { ?>
        <link rel="stylesheet" href="<?= $url ?>">
    <?php } ?>
    <style>
        <?php foreach ($stylesInline as $style) { ?>
        <?= $style ?>
        <?php } ?>
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center">Мини документация</h1>
            <b>Всего классов: <?= count($classesData) ?></b>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <b>Категории классов</b>
            <hr>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <?php foreach ($categories as $categoryName => $category) { ?>
                    <a class="nav-link" id="v-pills-<?= $categoryName ?>-tab" data-toggle="pill"
                       href="#v-pills-<?= $categoryName ?>" role="tab" aria-controls="v-pills-home"
                       aria-selected="true">
                        <?= $categoryName ?>
                        <span class="badge badge-secondary"><?= count($category) ?></span>
                    </a>
                <?php } ?>
                <a class="nav-link" id="v-pills-<?= $nocategoryLabel ?>-tab" data-toggle="pill"
                   href="#v-pills-<?= $nocategoryLabel ?>" role="tab" aria-controls="v-pills-home"
                   aria-selected="true">
                    Без категории
                    <span class="badge badge-secondary"><?= count($nocategory) ?></span>
                </a>
            </div>
        </div>
        <div class="col-md-9">
            <h2>Поиск</h2>
            <form action="">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary">Искать</button>
                    </div>
                    <input type="text" class="form-control" placeholder="Введите класс" aria-label="Username"
                           aria-describedby="basic-addon1">
                </div>
            </form>
            <hr>
            <div class="tab-content" id="v-pills-tabContent">
                <?php foreach ($categories as $categoryName => $category) { ?>
                    <div class="tab-pane fade show" id="v-pills-<?= $categoryName ?>" role="tabpanel"
                         aria-labelledby="v-pills-home-tab">
                        <h2 class="mb-4"><?= $categoryName ?></h2>
                        <?php foreach ($category as $item) { ?>
                            <div>
                                <a href="#">
                                    <?= $item['class'] ?>
                                </a>
                            </div>
                            <div>
                                <?php if (isset($item['description'])) { ?>
                                    <?= $item['description'] ?>
                                <?php } ?>
                            </div>
                            <?php if (isset($item['version'])) { ?>
                                <div>
                                    <?= $item['version'] ?>
                                </div>
                            <?php } ?>
                            <hr>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="tab-pane fade show" id="v-pills-<?= $nocategoryLabel ?>" role="tabpanel"
                     aria-labelledby="v-pills-home-tab">
                    <h2 class="mb-4"><?= $nocategoryLabel ?></h2>
                    <?php foreach ($nocategory as $item) { ?>
                        <div>
                            <a href="#">
                                <?= $item['class'] ?>
                            </a>
                        </div>
                        <?php if (isset($item['description'])) { ?>
                            <div>
                                <?= $item['description'] ?>
                            </div>
                        <?php } ?>
                        <?php if (isset($item['version'])) { ?>
                            <div>
                                <?= $item['version'] ?>
                            </div>
                        <?php } ?>
                        <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="text-center">
    <a href="mailto://kosuha606@gmail.com">kosuha606@gmail.com</a>
</footer>
<?php foreach ($scriptsUrl as $url) { ?>
    <script src="<?= $url ?>"></script>
<?php } ?>
</body>
</html>
