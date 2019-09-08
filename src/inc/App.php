<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot;

use StevoTVRBot\Page\Page;

/**
 * App entry point.
 */
class App
{
	/**
	 * Run the application.
	 */
	public static function run()
	{
		$options = [
			'regexp' => '/^[\w\-\/]*$/',
		];
		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_REGEXP, [ 'options' => $options ]) ?? 'index';
		$page = explode('/', rtrim($page, '/'));
		Page::route(array_shift($page), $page);
	}
}
