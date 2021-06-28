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
    // remove first element (\Blexr)
    unset($parts[0]);
    $classPath = implode("\\", $parts);

    include_once __DIR__ . "\\src\\$classPath.php";
});
