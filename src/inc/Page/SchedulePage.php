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
    	if (filter_input(INPUT_GET, 'json'))
    	{
	        header('Access-Control-Allow-Origin: *');
	        echo json_encode(ScheduleModel::get() ?? []);
    	}
    	else
    	{
    		$schedule = ScheduleModel::get();

    		if (is_array($schedule))
    		{
    			$data = [
    				'schedule'	=> [],
    			];

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
