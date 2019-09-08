<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\ItemsModel;
use StevoTVRBot\Model\SettingsModel;

/**
 * Handler for the !find command. This command finds a weighted random
 * item/modifier combination on behalf of the calling user and stores it in
 * their inventory. It returns the results of the search on success.
 *
 * Usage: !find
 * Returns: <user> found <item> worth $<value>
 */
class FindCommand extends Command
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

		$item = ItemsModel::find($user);
		if ($item)
		{
			printf('%s found %s worth %d %s', $item['user'], $item['description'], $item['value'], SettingsModel::getPointsName());
		}
		else
		{
			echo 'No items could be found.';
		}
	}
}
