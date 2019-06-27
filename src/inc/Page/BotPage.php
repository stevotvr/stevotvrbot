<?php

namespace StevoTVRBot\Page;

use StevoTVRBot\Command\Command;
use StevoTVRBot\Config;

class BotPage extends Page
{
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
