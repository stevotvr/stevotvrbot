<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Command;

use StevoTVRBot\Config;
use StevoTVRBot\Model\ItemsModel;
use StevoTVRBot\Model\SettingsModel;

/**
 * Handler for the !sell command. This command takes an item description as
 * input and attempts to sell the matching item from the inventory of the
 * calling user. The command rewards the user with the associated value on
 * success.
 *
 * Usage: !sell <item>
 * Returns: !addpoints <user> <value>
 */
class SellCommand extends Command
{
	/**
	 * @inheritDoc
	 */
	protected function exec(string $args, string $user = null)
	{
		if (!$user)
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
			echo '400 Bad Request';
			return;
		}

		if (!$args)
		{
			echo 'Usage: !sell <item>';
			return;
		}

		$sold = ItemsModel::sell($user, $args);
		if ($sold)
		{
			if ($this->addUserPoints($user, $sold['value']))
			{
				ItemsModel::addToStore($sold['itemId']);
				printf('%s sold %s for %d %s', $sold['user'], $args, $sold['value'], SettingsModel::getPointsName());
			}
			else
			{
				header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
				echo '503 Service Unavailable';
			}
		}
		else
		{
			printf('%s, that item could not be found in your inventory.', $user);
		}
	}

	/**
	 * Add points to a StreamElements user.
	 *
	 * @param string $user   The username of the user
	 * @param int    $points The number of points to add
	 *
	 * @return boolean True if the API request was successful, otherwise false
	 */
	private function addUserPoints(string $user, int $points)
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
