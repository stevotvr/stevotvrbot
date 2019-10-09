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
	 * Get the active schedule from the database.
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
				$schedule[] = compact('day', 'hour', 'minute', 'length', 'game', 'platform');
			}

			$stmt->close();

			return $schedule;
		}

		return false;
	}

	/**
	 * Get the schedule from the database.
	 *
	 * @return array The schedule
	 */
	public static function getSchedule()
	{
		if ($stmt = self::db()->prepare("SELECT id, day, hour, minute, length, game, platform, active FROM schedule ORDER BY day ASC, hour ASC, minute ASC;"))
		{
			$schedule = [];

			$stmt->execute();
			$stmt->bind_result($id, $day, $hour, $minute, $length, $game, $platform, $active);

			while ($stmt->fetch())
			{
				$schedule[] = compact('id', 'day', 'hour', 'minute', 'length', 'game', 'platform', 'active');
			}

			$stmt->close();

			return $schedule;
		}

		return false;
	}

	/**
	 * Set the schedule.
	 *
	 * @param array $schedule Array of associative arrays of schedule items
	 */
	public static function setSchedule(array $schedule)
	{
		if ($stmt = self::db()->prepare('DELETE FROM schedule;'))
		{
			$stmt->execute();
			$stmt->close();
		}

		if ($stmt = self::db()->prepare('INSERT INTO schedule (day, hour, minute, length, game, platform, active) VALUES (?, ?, ?, ?, ?, ?, ?);'))
		{
			$stmt->bind_param('iiiissi', $day, $hour, $minute, $length, $game, $platform, $active);
			foreach ($schedule as $item)
			{
				$day = max(0, min(6, $item['day']));
				$hour = max(0, min(23, $item['hour']));
				$minute = max(0, min(59, $item['minute']));
				$length = $item['length'];
				$game = $item['game'];
				$platform = $item['platform'];
				$active = (bool) $item['active'];
				$stmt->execute();
			}
			$stmt->close();
		}
	}
}
