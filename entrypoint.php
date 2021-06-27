<?php

require __DIR__ . '\\vendor\\autoload.php';
require __DIR__ . '\\Autoloader.php';

use Blexr\Router;

// Check and load .env config
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_DSN', 'SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'SMTP_EMAIL'])->notEmpty();
} catch (Dotenv\Exception\ValidationException $ex) {
    echo $ex->getMessage();
    exit();
}


Router::handleRequest();
