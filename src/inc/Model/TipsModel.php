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
 * Model representing the tips database.
 */
class TipsModel extends Model
{
	/**
	 * Add a tip to the database.
	 *
	 * @param string $user The username of the user adding the tip
	 * @param string $tip  The tip message
	 */
	public static function add(string $user, string $tip)
	{
		if ($stmt = self::db()->prepare("INSERT INTO tips (user, message) VALUES (?, ?);"))
		{
			$stmt->bind_param('ss', $user, $tip);
			$stmt->execute();
			$stmt->close();

			return self::db()->insert_id;
		}

		return false;
	}

	/**
	 * Get a random tip message from the database.
	 *
	 * @return string The tip message
	 */
	public static function get()
	{
		if ($stmt = self::db()->prepare("SELECT message FROM tips WHERE status = 'APPROVED' ORDER BY RAND() LIMIT 1;"))
		{
			$stmt->execute();
			$stmt->bind_result($message);
			$stmt->fetch();
			$stmt->close();

			return $message;
		}

		return false;
	}

	/**
	 * Get all the tips from the database in a random order.
	 *
	 * @return string[] The list of tip messages
	 */
	public static function getAll()
	{
		if ($stmt = self::db()->prepare("SELECT message FROM tips WHERE status = 'APPROVED' ORDER BY RAND();"))
		{
			$list = [];

			$stmt->execute();
			$stmt->bind_result($message);

			while ($stmt->fetch())
			{
				$list[] = $message;
			}

			$stmt->close();

			return $list;
		}

		return false;
	}
}
