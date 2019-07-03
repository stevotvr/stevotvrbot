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
		    printf('!addpoints %s %d', $sold['user'], $sold['value']);
 		}
 		else
 		{
            printf('%s, that item could not be found in your inventory.', $user);
 		}
	}
}
