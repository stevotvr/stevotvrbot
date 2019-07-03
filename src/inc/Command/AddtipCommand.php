<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\TipsModel;

/**
 * Handler for the !addtip command. This command takes a message as input and
 * inserts it into the database. Messages must be between 2 and 80 characters
 * long inclusive.
 *
 * Usage: !addtip
 * Returns: Tip #<id> has been added to the list
 */
class AddtipCommand extends Command
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

        $len = strlen($args);

        if ($len < 2)
        {
            echo $user . ' Your tip message is too short (2 characters min, yours was ' . $len . ')';
        }
        else if ($len > 80)
        {
            echo $user . ' Your tip message is too long (80 characters max, yours was ' . $len . ')';
        }
        else
        {
        	$id = TipsModel::add($user, $args);
            if ($id)
            {
                echo 'Tip #' . $id . ' has been added to the list';
            }
        }
	}
}
