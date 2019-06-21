<?php

namespace StevoTVRBot;

spl_autoload_register(function ($className)
{
    $path = explode('\\', $className);
    $path[0] = 'inc';
    $filePath = implode(DIRECTORY_SEPARATOR, $path) . '.php';
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $filePath))
    {
        require_once $filePath;
    }
});

App::run();
