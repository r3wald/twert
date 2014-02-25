<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/services.php';
$app->boot();
$app->run();
