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
	public static function get(string $key)
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
	public static function set(string $key, $value)
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

	/**
	 * Get the name of the StreamElements loyalty points.
	 *
	 * @return string The name of the StreamElements loyalty points
	 */
	public static function getPointsName()
	{
		$points_name = null;

		try
		{
			$points_name = self::get('se_points_name');
			$last_update = self::get('se_points_last_update');
			if (!isset($last_update) || (time() - $last_update > 120))
			{
				$ctx = stream_context_create([
					'http'	=> [
						'ignore_errors'	=> '1',
						'method'		=> 'GET',
						'header'		=> [
							'Accept: application/json',
							'Content-Type: Content-Type',
							'Authorization: Bearer ' . Config::SE_JWT_TOKEN,
						],
					],
				]);
				$url = sprintf('https://api.streamelements.com/kappa/v2/loyalty/%s', Config::SE_CHANNEL_ID);
				$stream = @fopen($url, 'r', false, $ctx);
				if ($stream)
				{
					$meta = stream_get_meta_data($stream);
					$status = array_shift($meta['wrapper_data']);
					$response_code = (int) substr($status, strpos($status, ' ') + 1, 3);
					$result = stream_get_contents($stream);
					fclose($stream);

					if ($response_code === 200)
					{
						$data = json_decode($result, true);
						if (is_array($data) && isset($data['loyalty'], $data['loyalty']['name']))
						{
							$points_name = $data['loyalty']['name'];
							self::set('se_points_name', $points_name);
						}
					}

					self::set('se_points_last_update', time());
				}
			}
		}
		catch (\Exception $e)
		{
		}

		return $points_name ?? 'points';
	}
}
