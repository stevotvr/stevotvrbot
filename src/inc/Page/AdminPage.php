<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\CommandsModel;
use StevoTVRBot\Model\ItemsModel;
use StevoTVRBot\Model\ScheduleModel;
use StevoTVRBot\Model\SettingsModel;
use StevoTVRBot\Model\TipsModel;
use StevoTVRBot\Model\UsersModel;

/**
 * Handler for the admin page.
 */
class AdminPage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$user = UsersModel::getCurrentUser();

		if ($user === null)
		{
			UsersModel::initAuthFlow('admin');
			return;
		}

		if (!$user['isAdmin'])
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
			echo '401 Unauthorized';
			return;
		}

		switch ($params[0])
		{
			case 'commands':
				$this->commands();
				break;
			case 'schedule':
				$this->schedule();
				break;
			case 'tips':
				$this->tips($params[1] ?? null, (int) ($params[2] ?? 0));
				break;
			default:
				$this->index();
		}
	}

	protected function commands()
	{
		$data = [
			'error' 		=> [],
			'levels'	=> [
				'Public',
				'Subscriber',
			],
			'commands'	=> [],
			'action'	=> '/admin/commands',
		];

		if (filter_has_var(INPUT_POST, 'action'))
		{
			switch (filter_input(INPUT_POST, 'action'))
			{
				case 'add':
					$options = [
						'options'	=> [
							'regexp'	=> '/^[\\w -]+$/i',
							'default'	=> 0,
							'min_range'	=> 0,
						],
					];

					$name = filter_input(INPUT_POST, 'command_name', FILTER_VALIDATE_REGEXP, $options);
					if (empty($name))
					{
						$data['error'][] = 'Invalid command name';
						break;
					}

					$arguments = filter_input(INPUT_POST, 'command_arguments') ?? '';
					$description = filter_input(INPUT_POST, 'command_description') ?? '';
					$level = filter_input(INPUT_POST, 'command_level', FILTER_VALIDATE_INT, $options) ?? 0;

					CommandsModel::addCommand(strtolower($name), $arguments, $description, $level);

					header('Location: /admin/commands');
					return;
				case 'update':
					$options = [
						'options'	=> [
							'regexp'	=> '/^[\\w -]+$/i',
							'min_range'	=> 0,
						],
						'flags'		=> FILTER_REQUIRE_ARRAY,
					];

					$names = filter_input(INPUT_POST, 'command_name', FILTER_VALIDATE_REGEXP, $options) ?? [];
					$arguments = filter_input(INPUT_POST, 'command_arguments', FILTER_DEFAULT, $options) ?? [];
					$descriptions = filter_input(INPUT_POST, 'command_description', FILTER_DEFAULT, $options) ?? [];
					$levels = filter_input(INPUT_POST, 'command_level', FILTER_VALIDATE_INT, $options) ?? [];
					$deletes = filter_input(INPUT_POST, 'command_delete', FILTER_VALIDATE_BOOLEAN, $options) ?? [];

					$nameCount = count($names);
					if ($nameCount !== count($arguments) || $commandCount !== count($descriptions) || $commandCount !== count($levels))
					{
						$data['error'][] = 'Invalid input';
						break;
					}

					$commands = [];
					foreach (array_keys($names) as $i)
					{
						if (isset($deletes[$i]))
						{
							continue;
						}

						if (empty($names[$i]))
						{
							$data['error'][] = 'Invalid command name';
							break;
						}

						$commands[] = [
							'name'			=> strtolower($names[$i]),
							'arguments'		=> $arguments[$i],
							'description'	=> $descriptions[$i],
							'level'			=> $levels[$i],
						];
					}

					if (!empty($data['error']))
					{
						break;
					}

					CommandsModel::updateCommands($commands);

					header('Location: /admin/commands');
					return;
			}
		}

		$commands = CommandsModel::getCommands();
		foreach ($commands as $command)
		{
			$data['commands'][] = [
				'id'			=> $command['id'],
				'name'			=> htmlspecialchars($command['command']),
				'arguments'		=> htmlspecialchars($command['arguments']),
				'description'	=> htmlspecialchars($command['description']),
				'level'			=> $command['level'],
			];
		}

		$this->showTemplate('admin/commands', $data);
	}

	protected function schedule()
	{
		$data = [
			'error'		=> [],
			'weekDays'	=> [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday',
			],
			'schedule'	=> [],
			'action'	=> '/admin/schedule',
		];

		if (filter_has_var(INPUT_POST, 'action'))
		{
			switch (filter_input(INPUT_POST, 'action'))
			{
				case 'update':
					$options = [
						'options'	=> [
							'default'	=> 0,
							'min_range'	=> 0,
						],
						'flags'		=> FILTER_REQUIRE_ARRAY,
					];

					$lengths = filter_input(INPUT_POST, 'schedule_length', FILTER_VALIDATE_INT, $options) ?? [];

					$options['options']['max_range'] = 6;
					$days = filter_input(INPUT_POST, 'schedule_day', FILTER_VALIDATE_INT, $options) ?? [];

					$options['options']['max_range'] = 23;
					$hours = filter_input(INPUT_POST, 'schedule_hour', FILTER_VALIDATE_INT, $options) ?? [];

					$options['options']['max_range'] = 59;
					$minutes = filter_input(INPUT_POST, 'schedule_minute', FILTER_VALIDATE_INT, $options) ?? [];

					$games = filter_input(INPUT_POST, 'schedule_game', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];
					$platforms = filter_input(INPUT_POST, 'schedule_platform', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];

					$count = count($lengths);
					if ($count !== count($days) || $count !== count($hours) || $count !== count($minutes) || $count !== count($games) || $count !== count($platforms))
					{
						$data['error'][] = 'Invalid input';
						break;
					}

					$actives = filter_input(INPUT_POST, 'schedule_active', FILTER_VALIDATE_BOOLEAN, FILTER_REQUIRE_ARRAY) ?? [];
					$deletes = filter_input(INPUT_POST, 'schedule_delete', FILTER_VALIDATE_BOOLEAN, FILTER_REQUIRE_ARRAY) ?? [];

					$schedule = [];
					foreach (array_keys($lengths) as $i)
					{
						if (isset($deletes[$i]))
						{
							continue;
						}

						$schedule[] = [
							'day'		=> $days[$i],
							'hour'		=> $hours[$i],
							'minute'	=> $minutes[$i],
							'length'	=> $lengths[$i],
							'game'		=> $games[$i],
							'platform'	=> $platforms[$i],
							'active'	=> isset($actives[$i]),
						];
					}

					ScheduleModel::setSchedule($schedule);

					header('Location: /admin/schedule');
					return;
				case 'add':

					header('Location: /admin/schedule');
					return;
			}
		}

		$schedule = ScheduleModel::getSchedule();
		foreach ($schedule as $day)
		{
			$data['schedule'][] = [
				'id'		=> $day['id'],
				'day'		=> $day['day'],
				'hour'		=> $day['hour'],
				'minute'	=> $day['minute'],
				'length'	=> $day['length'],
				'game'		=> htmlspecialchars($day['game']),
				'platform'	=> htmlspecialchars($day['platform']),
				'active'	=> (bool) $day['active'],
			];
		}

		$this->showTemplate('admin/schedule', $data);
	}

	protected function tips(string $page = null, int $tipId)
	{
		$data = [
			'error'			=> [],
			'page'			=> $page,
			'statusOptions'	=> [
				TipsModel::PENDING	=> 'Pending',
				TipsModel::APPROVED	=> 'Approved',
				TipsModel::REJECTED	=> 'Rejected',
			],
		];

		if (filter_has_var(INPUT_POST, 'action'))
		{
			switch (filter_input(INPUT_POST, 'action'))
			{
				case 'update':
					$message = filter_input(INPUT_POST, 'tip_message');
					if (empty($message))
					{
						$data['error'][] = 'Invalid tip message';
					}
					$status = filter_input(INPUT_POST, 'tip_status');

					if (!isset($data['statusOptions'][$status]))
					{
						$data['error'][] = 'Invalid tip status';
					}

					if (!empty($data['error']))
					{
						break;
					}

					$user = filter_input(INPUT_POST, 'tip_user') ?? '';

					$tipId = filter_input(INPUT_POST, 'tip_id', FILTER_VALIDATE_INT);
					if (!empty($tipId))
					{
						TipsModel::updateTip($tipId, $user, $message, $status);
					}
					else
					{
						TipsModel::addTip($user, $message, $status);
					}

					header('Location: /admin/tips');
					return;
				case 'delete':
					if (filter_has_var(INPUT_POST, 'confirm_delete'))
					{
						$id = filter_input(INPUT_POST, 'tip_id', FILTER_VALIDATE_INT);
						TipsModel::deleteTip($id);
					}

					header('Location: /admin/tips');
					return;
				case 'update_status':
					$approves = filter_input(INPUT_POST, 'approve_tip', FILTER_VALIDATE_BOOLEAN, FILTER_REQUIRE_ARRAY) ?? [];
					$rejects = filter_input(INPUT_POST, 'reject_tip', FILTER_VALIDATE_BOOLEAN, FILTER_REQUIRE_ARRAY) ?? [];

					$keys = array_merge(array_keys($approves), array_keys($rejects));

					foreach ($keys as $id)
					{
						TipsModel::updateStatus($id, isset($approves[$id]) ? TipsModel::APPROVED : TipsModel::REJECTED);
					}

					header('Location: /admin/tips');
					return;
			}
		}

		switch ($page)
		{
			case 'add':
				$data['action'] = '/admin/tips/add';
				$data['tip'] = [
					'user'		=> '',
					'message'	=> '',
					'status'	=> TipsModel::APPROVED,
				];
				break;
			case 'edit':
				$data['action'] = '/admin/tips/edit/' . $tipId;
			case 'delete':
				$data['action'] = '/admin/tips/delete/' . $tipId;;
				$tip = TipsModel::getTip($tipId);
				if (!$tip)
				{
					header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
					echo '404 Not Found';
					return;
				}

				$data['tip'] = [
					'id'		=> $tip['id'],
					'time'		=> htmlspecialchars($tip['time']),
					'user'		=> htmlspecialchars($tip['user']),
					'message'	=> htmlspecialchars($tip['message']),
					'status'	=> $tip['status'],
				];
				break;
			default:
				$data['action'] = '/admin/tips';
				$tips = TipsModel::getTips();
				$data['tips'] = [
					TipsModel::PENDING	=> [],
					TipsModel::APPROVED	=> [],
					TipsModel::REJECTED	=> [],
				];
				foreach ($tips as $tip)
				{
					$data['tips'][$tip['status']][] = [
						'id'		=> $tip['id'],
						'time'		=> htmlspecialchars($tip['time']),
						'user'		=> htmlspecialchars($tip['user']),
						'message'	=> htmlspecialchars($tip['message']),
						'status'	=> ucfirst($tip['status']),
					];
				}
		}

		$this->showTemplate('admin/tips', $data);
	}

	protected function index()
	{
		$this->showTemplate('admin');
	}
}
