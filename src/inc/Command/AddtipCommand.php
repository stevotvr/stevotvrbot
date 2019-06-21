<?php

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\TipsModel;

class AddtipCommand extends Command
{
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
