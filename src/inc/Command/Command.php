<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Command;

/**
 * Represents a chat command that can be called by the bot Page.
 */
abstract class Command
{
	/**
	 * Route a chat command to the handler Command.
	 */
	public static function route()
	{
		$input = filter_input(INPUT_GET, 'input');
		list($command, $args) = explode(' ', ltrim($input, '!'), 2);

		$className = 'StevoTVRBot\\Command\\' . ucfirst(strtolower($command)) . 'Command';
		if (class_exists($className))
		{
			$object = new $className();
			$object->exec($args ?? '', self::getUser());
		}
		else
		{
	        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	        echo '404 Not Found';
		}
	}

	/**
	 * Execute the command.
	 *
	 * @param string      $args The arguments string supplied to the command
	 * @param string|null $user The username of the user executing the command,
	 *                          or null if not supplied
	 */
	protected abstract function exec(string $args, string $user = null);

	/**
	 * Get the validated Twitch username from the GET parameters.
	 *
	 * @return string|null The username, or null if no valid username is
	 *                     supplied
	 */
    private static function getUser()
    {
    	$options = [
    		'regexp' => '/^[0-9a-zA-Z]\w{1,24}$/',
    	];
    	return filter_input(INPUT_GET, 'user', FILTER_VALIDATE_REGEXP, [ 'options' => $options ]);
    }
}
