<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

/**
 * Represents a handler for a page, a base unit of the application.
 */
abstract class Page
{
	/**
	 * Route a request to the handler Page.
	 *
	 * @param string   $page   The name of the page
	 * @param string[] $params The list of parameters to send to the handler
	 */
	public static function route(string $page, array $params)
	{
		$page = empty($page) ? 'index' : $page;
		$className = 'StevoTVRBot\\Page\\' . ucfirst(strtolower($page)) . 'Page';
		if (class_exists($className))
		{
			$object = new $className();
			$object->run($params);
		}
		else
		{
	        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	        echo '404 Not Found';
		}
	}

	/**
	 * Run the Page.
	 *
	 * @param string[] $params The list of parameters
	 */
    protected abstract function run(array $params);

    /**
     * Show a template.
     *
     * @param string $template The name of the template
     * @param array  $data     The data with which to populate the template
     */
    protected final function showTemplate(string $template, array $data = array())
    {
    	$dir = __DIR__ . '/../views/';
    	extract($data);
    	require $dir . 'header.php';
    	require $dir . $template . '/index.php';
    	require $dir . 'footer.php';
    }
}
