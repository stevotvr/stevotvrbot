<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

/**
 * Model representing the commands database.
 */
class CommandsModel extends Model
{
 	/**
 	 * Get the list of available chat bot commands.
 	 *
 	 * @return array|boolean Array containing chat command data, or false on
 	 *                       failure
 	 */
 	public static function getCommands()
 	{
 		if ($stmt = self::db()->prepare("SELECT command, arguments, description, level FROM commands ORDER BY command ASC;"))
 		{
 			$commands = [];

 			$stmt->execute();
 			$stmt->bind_result($command, $arguments, $description, $level);

 			while ($stmt->fetch())
 			{
 				$commands[] = [
 					'command'		=> $command,
 					'arguments'		=> $arguments,
 					'description'	=> $description,
 					'level'			=> $level,
 				];
 			}

 			$stmt->close();

 			return $commands;
 		}

 		return false;
 	}
}
