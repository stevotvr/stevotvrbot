<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\TipsModel;

/**
 * Handler for the tips page, which outputs a randomly ordered JSON array of
 * all tips in the database.
 */
class TipsPage extends Page
{
	/**
	 * @inheritDoc
	 */
    public function run(array $params)
    {
        header('Access-Control-Allow-Origin: *');
        echo json_encode(TipsModel::getAll() ?? []);
    }
}
