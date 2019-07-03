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
 * Handler for the !tip command. This command shows a random tip message.
 *
 * Usage: !tip
 * Returns: <A random tip message from the database>
 */
class TipCommand extends Command
{
	/**
	 * @inheritDoc
	 */
	protected function exec(string $args, string $user = null)
	{
		$message = TipsModel::get();
		if ($message)
		{
			echo $message;
		}
	}
}
