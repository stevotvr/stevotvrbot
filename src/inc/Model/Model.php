<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

use StevoTVRBot\Config;

/**
 * Represents a data model for interaction with the database.
 */
abstract class Model
{
	/**
	 * The object representing the database connection.
	 *
	 * @var \mysqli
	 */
	private static $db;

	/**
	 * Get the object representing the database connection.
	 *
	 * @return \mysqli
	 */
    protected static function db(): \mysqli
    {
        if (!self::$db)
        {
            self::$db = new \mysqli(Config::DBHOST, Config::DBUSER, Config::DBPASS, Config::DBNAME);
        }

        return self::$db;
    }
}
