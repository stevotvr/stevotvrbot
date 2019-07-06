<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

use StevoTVRBot\Config;

/**
 * Model representing the settings database.
 */
class SettingsModel extends Model
{
	/**
	 * The cached settings.
	 *
	 * @var string[]
	 */
	private static $settings;

 	/**
 	 * Get a setting from the database.
 	 *
 	 * @param string $key The name of the setting
 	 *
 	 * @return string The value of the setting
 	 *
 	 * @throws \Exception The settings could not be loaded
 	 */
 	public function get(string $key)
 	{
 		if (!is_array(self::$settings))
 		{
	 		if ($stmt = self::db()->prepare("SELECT setting, value FROM settings;"))
	 		{
	 			$settings = [];

	 			$stmt->execute();
	 			$stmt->bind_result($setting, $value);

	 			while ($stmt->fetch())
	 			{
	 				$settings[$setting] = $value;
	 			}

	 			$stmt->close();

	 			self::$settings = $settings;
	 		}
	 		else
	 		{
	 			throw new \Exception('Error loading settings from the database');
	 		}
 		}

 		return self::$settings[$key] ?? null;
 	}

 	/**
 	 * Set the value of a setting in the database.
 	 *
 	 * @param string $key   The name of the setting
 	 * @param mixed  $value The value to set
 	 *
 	 * @return boolean True on success, otherwise false
 	 */
 	public function set(string $key, $value)
 	{
        if ($stmt = self::db()->prepare("INSERT INTO settings (setting, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?;"))
        {
            $stmt->bind_param('sss', $key, $value, $value);
            $result = $stmt->execute();
            $stmt->close();

            if ($result && is_array(self::$settings))
            {
            	self::$settings[$key] = $value;
            }

            return $result;
        }

        return false;
 	}
}
