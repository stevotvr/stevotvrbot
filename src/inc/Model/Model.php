<?php

namespace StevoTVRBot\Model;

use StevoTVRBot\Config;

abstract class Model
{
	private static $db;

    protected static function db(): \mysqli
    {
        if (!self::$db)
        {
            self::$db = new \mysqli(Config::DBHOST, Config::DBUSER, Config::DBPASS, Config::DBNAME);
        }

        return self::$db;
    }
}
