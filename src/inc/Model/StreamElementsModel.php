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
 * Model representing the StreamElements backend..
 */
class StreamElementsModel extends Model
{
	/**
	 * Add points to a StreamElements user.
	 *
	 * @param string $user   The username of the user
	 * @param int    $points The number of points to add
	 *
	 * @return boolean True if the API request was successful, otherwise false
	 */
	public static function addUserPoints(string $user, int $points)
	{
		$ctx = stream_context_create([
			'http'	=> [
				'ignore_errors'	=> '1',
				'method'		=> 'PUT',
				'header'		=> [
					'Accept: application/json',
					'Content-Type: Content-Type',
					'Authorization: Bearer ' . Config::SE_JWT_TOKEN,
				],
			],
		]);
		$url = sprintf('https://api.streamelements.com/kappa/v2/points/%s/%s/%d', Config::SE_CHANNEL_ID, $user, $points);
		$stream = @fopen($url, 'r', false, $ctx);
		if (!$stream)
		{
			return false;
		}

		$meta = stream_get_meta_data($stream);
		$status = array_shift($meta['wrapper_data']);
		$response_code = (int) substr($status, strpos($status, ' ') + 1, 3);
		fclose($stream);

		return $response_code === 200;
	}
}
