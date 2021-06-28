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
