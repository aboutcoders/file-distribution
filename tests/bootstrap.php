<?php

$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('Abc\\', __DIR__.'/Abc');

date_default_timezone_set('UTC');
