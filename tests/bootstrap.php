<?php

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Abc\\', __DIR__.'/unit/Abc');
$loader->addPsr4('Abc\\', __DIR__.'/integration/Abc');

date_default_timezone_set('UTC');
