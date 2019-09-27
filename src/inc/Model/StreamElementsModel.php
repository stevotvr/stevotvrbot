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
		$url = sprintf('https://api.streamelements.com/kappa/v2/points/%s/%s/%d', Config::SE_CHANNEL_ID, $user, $points);
		$ch = curl_init($url);
		if ($ch === false)
		{
			return false;
		}

		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-Type: Content-Type',
			'Authorization: Bearer ' . Config::SE_JWT_TOKEN,
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

		curl_close($ch);

		return $responseCode === 200;
	}

	/**
	 * Get the number of points from a StreamElements user.
	 *
	 * @param string $user The username of the user
	 *
	 * @return int|boolean The number of points, or false on failure
	 */
	public static function getUserPoints(string $user)
	{
		$url = sprintf('https://api.streamelements.com/kappa/v2/points/%s/%s', Config::SE_CHANNEL_ID, $user);
		$ch = curl_init($url);
		if ($ch === false)
		{
			return false;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'Content-Type: Content-Type',
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

		curl_close($ch);

		if ($responseCode === 200)
		{
			$response = json_decode($response, true);
			return is_array($response) ? $response['points'] : false;
		}

		return false;
	}
}
