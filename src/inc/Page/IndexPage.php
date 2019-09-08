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
 * Handler for the index page, which is the default page.
 */
class IndexPage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$this->showTemplate('index');
	}
}
