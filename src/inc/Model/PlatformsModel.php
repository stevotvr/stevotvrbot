<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2020, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

/**
 * Model representing the platforms database.
 */
class PlatformsModel extends Model
{
	/**
	 * Get the list of available streaming platforms.
	 *
	 * @return array|boolean Array containing streaming platforms, or false on
	 *                       failure
	 */
	public static function getPlatforms()
	{
		if ($stmt = self::db()->prepare("SELECT id, name, url FROM platforms ORDER BY name ASC;"))
		{
			$platforms = [];

			$stmt->execute();
			$stmt->bind_result($id, $name, $url);

			while ($stmt->fetch())
			{
				$platforms[] = [
					'id'	=> $id,
					'name'	=> $name,
					'url'	=> $url,
				];
			}

			$stmt->close();

			return $platforms;
		}

		return false;
	}
}
