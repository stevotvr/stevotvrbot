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

switch (filter_input(INPUT_GET, 'page'))
{
	case 'tips':
		(new TipsBot())->exec();
		break;
	case 'items':
		(new ItemsBot())->exec();
		break;
	default:
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        echo '404 Not Found';
}
