<?php

spl_autoload_extensions(".php");

/*
 * Replace first folder from namespace with the actual folder
 *
 * example:
 * \Blexr\Some\Path => \src\Some\Path
 *
 */

spl_autoload_register(function ($fullQualifiedClassName) {
    $parts = explode('\\', $fullQualifiedClassName);
    // remove root folder
    unset($parts[0]);
    $classPath = implode("\\", $parts);

    // concat proper fodler (/src)
    include_once __DIR__ . "\\src\\$classPath.php";
});


// Check and load .env config
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required([
        'DB_HOST',
        'DB_NAME',
        'DB_USER',
        'DB_PASSWORD',
        'DB_DSN',
        'SMTP_HOST',
        'SMTP_PORT',
        'SMTP_USERNAME',
        'SMTP_PASSWORD',
        'SMTP_EMAIL',
        'BASE_URL'
    ])->notEmpty();
} catch (Dotenv\Exception\ValidationException $ex) {
    echo $ex->getMessage();
    exit();
}