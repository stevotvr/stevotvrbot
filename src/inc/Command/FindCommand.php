<?php

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\ItemsModel;

class FindCommand extends Command
{
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
	        printf('%s found %s worth $%d', $item['user'], $item['description'], $item['value']);
 		}
 		else
 		{
	        echo 'No items could be found.';
 		}
	}
}
