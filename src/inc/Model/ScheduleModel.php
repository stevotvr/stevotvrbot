<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

/**
 * Model representing the schedule database.
 */
class ScheduleModel extends Model
{
    /**
     * Get the schedule from the database.
     *
     * @return array The schedule
     */
    public static function get()
    {
        if ($stmt = self::db()->prepare("SELECT day, hour, minute, length, game, platform FROM schedule WHERE active = 1 ORDER BY day ASC, hour ASC, minute ASC;"))
        {
	        $schedule = [];

            $stmt->execute();
            $stmt->bind_result($day, $hour, $minute, $length, $game, $platform);

            while ($stmt->fetch())
            {
                $schedule[] = [
                	'day'		=> $day,
                	'hour'		=> $hour,
                	'minute'	=> $minute,
                	'length'	=> $length,
                	'game'		=> $game,
                	'platform'	=> $platform,
                ];
            }

            $stmt->close();

            return $schedule;
        }

        return false;
    }
}
