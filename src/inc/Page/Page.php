<?php

namespace StevoTVRBot\Page;

abstract class Page
{
	public static function route($page)
	{
		$object = null;

		switch ($page)
		{
			case 'bot':
				$object = new BotPage();
				break;
			case 'tips':
				$object = new TipsPage();
				break;
			default:
		        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		        echo '404 Not Found';
		        return;
		}

		$object->run();
	}

    protected abstract function run();
}
