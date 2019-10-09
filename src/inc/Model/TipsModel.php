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
	const PENDING = 'PENDING';
	const APPROVED = 'APPROVED';
	const REJECTED = 'REJECTED';

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

	/**
	 * Get a tip from the database.
	 *
	 * @param int $tipId The ID of the tip
	 *
	 * @return array|boolean The tip data, or false on failure
	 */
	public static function getTip(int $tipId)
	{
		if ($stmt = self::db()->prepare("SELECT id, time, user, message, status FROM tips WHERE id = ?;"))
		{
			$stmt->bind_param('i', $tipId);
			$stmt->execute();
			$stmt->bind_result($id, $time, $user, $message, $status);
			$stmt->fetch();
			$stmt->close();

			return compact('id', 'time', 'user', 'message', 'status');
		}

		return false;
	}

	/**
	 * Get all the tips from the database.
	 *
	 * @return array|boolean Array of tips, or false on failure
	 */
	public static function getTips()
	{
		if ($stmt = self::db()->prepare("SELECT id, time, user, message, status FROM tips ORDER BY time ASC;"))
		{
			$tips = [];

			$stmt->execute();
			$stmt->bind_result($id, $time, $user, $message, $status);

			while ($stmt->fetch())
			{
				$tips[] = compact('id', 'time', 'user', 'message', 'status');
			}

			$stmt->close();

			return $tips;
		}

		return false;
	}

	/**
	 * Add a tip to the database.
	 *
	 * @param string $user    The user that created the tip
	 * @param string $message The tip message
	 * @param string $status  The status of the tip
	 *
	 * @return int The ID of the tip
	 */
	public static function addTip(string $user = null, string $message, string $status)
	{
		if ($stmt = self::db()->prepare("INSERT INTO tips (user, message, status) VALUES (?, ?, ?);"))
		{
			$stmt->bind_param('sss', $user, $message, $status);
			$success = $stmt->execute();
			$stmt->close();

			if ($success)
			{
				return self::db()->insert_id;
			}
		}

		return 0;
	}

	/**
	 * Update a tip record.
	 *
	 * @param int    $tipId   The ID of the tip
	 * @param string $user    The user that created the tip
	 * @param string $message The tip message
	 * @param string $status  The status of the tip
	 */
	public static function updateTip(int $tipId, string $user = null, string $message, string $status)
	{
		if ($stmt = self::db()->prepare("UPDATE tips SET user = ?, message = ?, status = ? WHERE id = ?;"))
		{
			$stmt->bind_param('sssi', $user, $message, $status, $status);
			$stmt->execute();
			$stmt->close();
		}
	}

	/**
	 * Update the status of a tip.
	 *
	 * @param int    $tipId  The ID of the tip
	 * @param string $status The status of the tip
	 */
	public static function updateStatus(int $tipId, string $status)
	{
		if ($stmt = self::db()->prepare("UPDATE tips SET status = ? WHERE id = ?;"))
		{
			$stmt->bind_param('si', $status, $tipId);
			$stmt->execute();
			$stmt->close();
		}
	}

	/**
	 * Delete a tip record from the database.
	 *
	 * @param int $tipId The ID of the tip
	 */
	public static function deleteTip(int $tipId)
	{
		if ($stmt = self::db()->prepare("DELETE FROM tips WHERE id = ?;"))
		{
			$stmt->bind_param('i', $tipId);
			$stmt->execute();
			$stmt->close();
		}
	}
}
