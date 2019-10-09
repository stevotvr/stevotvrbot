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

/**
 * Handler for the !craft command. This command finds the recipe for the
 * requested item if it exists, checks that the user has the required items in
 * their inventory, and then converts the ingredients into the requested item
 * in the user's inventory. It returns the result of the crafting attempt.
 *
 * Usage: !craft <item>
 * Returns: <user> crafted <item>
 */
class CraftCommand extends Command
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
			echo 'Usage: !craft <item>';
			return;
		}

		$itemInfo = ItemsModel::findItem($args);
		if (!$itemInfo)
		{
			printf('Unknown item: %s', $args);
			return;
		}

		$ingredients = ItemsModel::getRecipe($itemInfo['id']);
		if (!$ingredients)
		{
			printf('%s cannot be crafted', $itemInfo['namePlural']);
			return;
		}

		$inventory = ItemsModel::getInventory($user);
		if (!is_array($inventory))
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
			echo '503 Service Unavailable';
			return;
		}

		$userItems = [];
		foreach ($inventory as $item)
		{
			$userItems[$item['itemId']] = $item['quantity'];
		}

		foreach ($ingredients as $item)
		{
			if (!isset($userItems[$item['itemId']]) || $userItems[$item['itemId']] < $item['quantity'])
			{
				printf('%s, you do not have all of the required ingredients to craft %s', $user, $itemInfo['nameSingle']);
				return;
			}
		}

		ItemsModel::takeItems($user, $ingredients);
		if (ItemsModel::giveItem($user, $itemInfo['id']))
		{
			printf('%s crafted %s', $user, $itemInfo['nameSingle']);
		}
	}
}
