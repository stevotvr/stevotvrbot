<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

use StevoTVRBot\Config;
use StevoTVRBot\Model\SettingsModel;
use StevoTVRBot\Model\PlatformsModel;
use StevoTVRBot\Model\ScheduleModel;

/**
 * Handler for the schedule page, which shows the current streaming schedule.
 */
class SchedulePage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$schedule = ScheduleModel::get() ?? [];
		$singleGame = SettingsModel::get('schedule_single_game') === '1';

		$platforms = [];
		foreach (PlatformsModel::getPlatforms() ?? [] as $platform)
		{
			$platforms[$platform['id']] = [
				'name'	=> htmlspecialchars($platform['name']),
				'url'	=> htmlspecialchars($platform['url']),
			];
		}

		if (filter_input(INPUT_GET, 'json'))
		{
			header('Access-Control-Allow-Origin: *');

			if ($singleGame)
			{
				$game = SettingsModel::get('schedule_game');
				$platform = SettingsModel::get('schedule_platform');
				foreach ($schedule as &$item)
				{
					$item['game'] = $game;
					$item['platform'] = $platforms[$platform];
				}
			}

			echo json_encode($schedule);
		}
		else
		{
			$data = [
				'singleGame'	=> $singleGame,
				'schedule'		=> [],
			];

			if ($singleGame)
			{
				$data['game'] = htmlspecialchars(SettingsModel::get('schedule_game'));;
			}

			$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
			$dt = new \DateTime('now', new \DateTimeZone(Config::TIMEZONE));

			foreach ($schedule as $item)
			{
				$dt->setTime($item['hour'], $item['minute']);
				$data['schedule'][] = [
					'day'		=> $days[$item['day']],
					'time'		=> htmlspecialchars($dt->format('g:ia T')),
					'game'		=> $singleGame ? null : htmlspecialchars($item['game']),
					'platform'	=> $platforms[$item['platform']],
				];
			}

			$this->showTemplate('schedule', $data);
		}
	}
}
