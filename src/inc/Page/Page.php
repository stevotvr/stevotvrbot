<?php

namespace StevoTVRBot\Page;

abstract class Page
{
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

    protected abstract function run(array $params);

    protected final function showTemplate(string $template, array $data = array())
    {
    	$dir = __DIR__ . '/../views/';
    	extract($data);
    	require $dir . 'header.php';
    	require $dir . $template . '/index.php';
    	require $dir . 'footer.php';
    }
}
