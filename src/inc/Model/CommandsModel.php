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

	/**
	 * Update the list of chat commands.
	 * 
	 * @param array[] The data for the commands
	 */
	public static function updateCommands(array $commands)
	{
		if ($stmt = self::db()->prepare("DELETE FROM commands;"))
		{
			$stmt->execute();
			$stmt->close();
		}

		if ($stmt = self::db()->prepare("INSERT INTO commands (command, arguments, description, level) VALUES (?, ?, ?, ?);"))
		{
			$stmt->bind_param('sssi', $name, $arguments, $description, $level);
			foreach ($commands as $command)
			{
				if (!isset($command['name'], $command['arguments'], $command['description'], $command['level']))
				{
					continue;
				}

				$name = $command['name'];
				$arguments = $command['arguments'];
				$description = $command['description'];
				$level = $command['level'];
				$stmt->execute();
			}
			$stmt->close();
		}
	}

	/**
	 * Add a chat command to the database.
	 *
	 * @param string $command     The command trigger
	 * @param string $arguments   The arguments for the command
	 * @param string $description The description of the command
	 * @param int    $level       The user level required to execute the
	 *                            command
	 */
	public static function addCommand(string $command, string $arguments, string $description, int $level)
	{
		if ($stmt = self::db()->prepare("INSERT INTO commands (command, arguments, description, level) VALUES (?, ?, ?, ?);"))
		{
			$stmt->bind_param('sssi', $command, $arguments, $description, $level);
			$stmt->execute();
			$stmt->close();
		}
	}

	/**
	 * Delete a chat command.
	 *
	 * @param int $id The ID of the command to delete
	 */
	public static function deleteCommand(int $id)
	{
		if ($stmt = self::db()->prepare("DELETE FROM commands WHERE id = ?;"))
		{
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->close();
		}
	}
}
