<?php

namespace StevoTVRBot;

use StevoTVRBot\Page\Page;

class App
{
	public static function run()
	{
		Page::route(filter_input(INPUT_GET, 'page'));
	}
}
