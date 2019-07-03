<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

use StevoTVRBot\Command\Command;
use StevoTVRBot\Config;

/**
 * Handler for the bot page, which routes calls from the chat bot to the
 * command handler.
 */
class BotPage extends Page
{
	/**
	 * @inheritDoc
	 */
    public function run(array $params)
    {
        if (filter_input(INPUT_GET, 'secret') !== Config::SECRET)
        {
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            echo '401 Unauthorized';
            return;
        }

        Command::route();
    }
}
