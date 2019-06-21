<?php

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\ItemsModel;

class SellCommand extends Command
{
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
