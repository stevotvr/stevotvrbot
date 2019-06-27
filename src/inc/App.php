<?php

namespace StevoTVRBot;

use StevoTVRBot\Page\Page;

class App
{
	public static function run()
	{
    	$options = [
    		'regexp' => '/^[\w\-\/]*$/',
    	];
		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_REGEXP, [ 'options' => $options ]) ?? 'index';
		$page = explode('/', $page);
		Page::route(array_shift($page), $page);
	}
}
