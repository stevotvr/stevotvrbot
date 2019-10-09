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
 * Handler for the !store command. This command takes an item description as
 * input and attempts to buy or sell the matching item. The command adds or
 * removes the required points from the user on success.
 *
 * Usage: !store <buy|sell> <item>
 * Returns: <Response from the store>
 */
class StoreCommand extends Command
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
			echo 'Usage: !store <buy|sell> <item>';
			return;
		}

		$args = explode(' ', $args, 2);
		if (count($args) < 2)
		{
			echo 'Usage: !store <buy|sell> <item>';
			return;
		}

		$itemInfo = ItemsModel::findItem($args[1]);
		if (!$itemInfo)
		{
			printf('Unknown item: %s', $args[1]);
			return;
		}

		switch ($args[0])
		{
			case 'buy':
				$this->buy($user, $itemInfo);
				break;
			case 'sell':
				$this->sell($user, $itemInfo);
				break;
			default:
				echo 'Usage: !store <buy|sell> <item>';
				return;
		}
	}

	/**
	 * Handle the buy command.
	 *
	 * @param string $user     The user calling the command
	 * @param array  $itemInfo The item being bought
	 */
	protected function buy(string $user, array $itemInfo)
	{
		if ($itemInfo['quantity'] < 1)
		{
			printf('%s, that item is out of stock.', $user);
		}
		elseif (StreamElementsModel::getUserPoints($user) < $itemInfo['value'])
		{
			printf('%s, you do not have enough %s to buy %s (costs %d %s).', $user, SettingsModel::getPointsName(), $itemInfo['nameSingle'], $itemInfo['value'], SettingsModel::getPointsName());
		}
		elseif (StreamElementsModel::addUserPoints($user, -$itemInfo['value']))
		{
			ItemsModel::buy($user, $itemInfo['id']);
			printf('%s bought %s for %d %s', $user, $itemInfo['nameSingle'], $itemInfo['value'], SettingsModel::getPointsName());
		}
		else
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
			echo '503 Service Unavailable';
		}
	}

	/**
	 * Handle the sell command.
	 *
	 * @param string $user     The user calling the command
	 * @param array  $itemInfo The item being sold
	 */
	protected function sell(string $user, array $itemInfo)
	{
		if (ItemsModel::sell($user, $itemInfo['id']))
		{
			if (StreamElementsModel::addUserPoints($user, $itemInfo['value']))
			{
				ItemsModel::addToStore($itemInfo['id']);
				printf('%s sold %s for %d %s', $user, $itemInfo['nameSingle'], $itemInfo['value'], SettingsModel::getPointsName());
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
}
