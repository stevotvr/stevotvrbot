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
use StevoTVRBot\Model\StreamElementsModel;

/**
 * Handler for the !buy command. This command takes an item description as
 * input and attempts to buy the matching item from the item store. The command
 * removes the required points from the user on success.
 *
 * Usage: !buy <item>
 * Returns: !addpoints <user> -<value>
 */
class BuyCommand extends Command
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
			echo 'Usage: !buy <item>';
			return;
		}

		$item = ItemsModel::getStoreItem($args);
		if ($item)
		{
			if ($item['quantity'] < 1)
			{
				printf('%s, that item is out of stock.', $user);
			}
			elseif (StreamElementsModel::getUserPoints($user) < $item['value'])
			{
				printf('%s, you do not have enough %s to buy %s (costs %d %s).', $user, SettingsModel::getPointsName(), $item['description'], $item['value'], SettingsModel::getPointsName());
			}
			elseif (StreamElementsModel::addUserPoints($user, -$item['value']))
			{
				ItemsModel::buy($user, $args);
				printf('%s bought %s for %d %s', $item['user'], $item['description'], $item['value'], SettingsModel::getPointsName());
			}
			else
			{
				header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
				echo '503 Service Unavailable';
			}
		}
		else
		{
			printf('%s, that item does not exist.', $user);
		}
	}
}
