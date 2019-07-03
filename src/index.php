<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

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
