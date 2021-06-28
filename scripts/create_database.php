<?php

require __DIR__ . '\\..\\vendor\\autoload.php';

// Check and load .env config
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '\\..\\');
    $dotenv->load();
    $dotenv->required([
        'DB_HOST',
        'DB_NAME',
        'DB_USER',
        'DB_PASSWORD'
    ])->notEmpty();
} catch (Dotenv\Exception\ValidationException $ex) {
    echo $ex->getMessage();
    exit();
}

echo "Creating database '{$_ENV['DB_NAME']}' at '{$_ENV['DB_HOST']}'\n";

$dbConnection = new \PDO('mysql:host=' . $_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

$query = $dbConnection->exec('CREATE DATABASE IF NOT EXISTS `' . $_ENV['DB_NAME'] . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;');

// close connection
unset($dbConnection);

echo "Importing file...\n";
$dbConnection = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$importFile = file_get_contents(__DIR__ . '\\create_database.sql');

$dbConnection->exec($importFile);
echo "Done !\n\n";
