<?php

namespace StevoTVRBot\Model;

class TipsModel extends Model
{
    public static function add(string $user, string $tip)
    {
        if ($stmt = self::db()->prepare("INSERT INTO tips (user, message) VALUES (?, ?);"))
        {
            $stmt->bind_param('ss', $user, $tip);
            $stmt->execute();
            $stmt->close();

            return self::db()->insert_id;
        }

        return false;
    }

    public static function get()
    {
        if ($stmt = self::db()->prepare("SELECT message FROM tips WHERE status = 'APPROVED' ORDER BY RAND() LIMIT 1;"))
        {
            $stmt->execute();
            $stmt->bind_result($message);
            $stmt->fetch();
            $stmt->close();

            return $message;
        }

        return false;
    }

    public static function getAll()
    {
        if ($stmt = self::db()->prepare("SELECT message FROM tips WHERE status = 'APPROVED' ORDER BY RAND();"))
        {
	        $list = [];

            $stmt->execute();
            $stmt->bind_result($message);

            while ($stmt->fetch())
            {
                $list[] = $message;
            }

            $stmt->close();

            return $list;
        }

        return false;
    }
}
