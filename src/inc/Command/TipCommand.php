<?php

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\TipsModel;

class TipCommand extends Command
{
	protected function exec(string $args, string $user = null)
	{
		$message = TipsModel::get();
		if ($message)
		{
			echo $message;
		}
	}
}
