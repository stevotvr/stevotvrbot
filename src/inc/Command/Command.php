<?php

namespace StevoTVRBot\Command;

abstract class Command
{
	public static function route()
	{
		$input = filter_input(INPUT_GET, 'input');
		list($command, $args) = explode(' ', $input, 2);

		$object = null;

		switch ($command)
		{
			case '!addtip':
				$object = new AddtipCommand();
				break;
			case '!find':
				$object = new FindCommand();
				break;
			case '!sell':
				$object = new SellCommand();
				break;
			case '!tip':
				$object = new TipCommand();
				break;
			default:
		        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		        echo '404 Not Found';
		        return;
		}

		$object->exec($args ?? '', self::getUser());
	}

	protected abstract function exec(string $args, string $user = null);

    private static function getUser()
    {
    	$options = [
    		'regexp' => '/^[0-9a-zA-Z]\w{1,24}$/',
    	];
    	return filter_input(INPUT_GET, 'user', FILTER_VALIDATE_REGEXP, [ 'options' => $options ]);
    }
}
