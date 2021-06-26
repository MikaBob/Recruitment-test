<?php

require __DIR__ . '\\vendor\\autoload.php';
require __DIR__ . '\\Autoloader.php';

use Blexr\Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


Router::handleRequest();
