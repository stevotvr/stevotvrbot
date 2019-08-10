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
		$schedule = ScheduleModel::get();
		$singleGame = SettingsModel::get('schedule_single_game') === '1';

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
            		$item['platform'] = $platform;
            	}
            }

	        echo json_encode($schedule ?? []);
    	}
    	else
    	{

    		if (is_array($schedule))
    		{
    			$data = [
    				'singleGame'	=> $singleGame,
    				'schedule'		=> [],
    			];

    			if ($singleGame)
    			{
    				$data['game'] = htmlspecialchars(sprintf('%s (%s)', SettingsModel::get('schedule_game'), SettingsModel::get('schedule_platform')));;
    			}

    			$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    			$dt = new \DateTime('now', new \DateTimeZone(Config::TIMEZONE));

    			foreach ($schedule as $item)
    			{
    				$dt->setTime($item['hour'], $item['minute']);
    				$data['schedule'][] = [
    					'day'	=> $days[$item['day']],
    					'time'	=> htmlspecialchars($dt->format('g:ia T')),
    					'game'	=> htmlspecialchars(sprintf('%s (%s)', $item['game'], $item['platform'])),
    				];
    			}

    			$this->showTemplate('schedule', $data);
    		}
    	}
    }
}
