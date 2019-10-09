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
			case 'items':
				$this->items($params[1] ?? null, $params[2] ?? null);
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

	protected function items(string $page = null, string $slug = null)
	{
		$data = [
			'error'	=> [],
			'page'	=> $page,
		];

		if (filter_has_var(INPUT_POST, 'action'))
		{
			switch (filter_input(INPUT_POST, 'action'))
			{
				case 'update':
					if (filter_has_var(INPUT_POST, 'update_ingredients'))
					{
						break;
					}

					$options = [
						'options'	=> [
							'regexp'	=> '/^[\\w -]+$/i',
							'default'	=> 0,
							'min_range'	=> 0,
						],
					];

					$name = filter_input(INPUT_POST, 'item_name', FILTER_VALIDATE_REGEXP, $options);
					if (empty($name))
					{
						$data['error'][] = 'Invalid item name';
					}

					$nameSingle = filter_input(INPUT_POST, 'item_name_single');
					if (empty($nameSingle))
					{
						$data['error'][] = 'Invalid singular item name';
					}

					$namePlural = filter_input(INPUT_POST, 'item_name_plural');
					if (empty($namePlural))
					{
						$data['error'][] = 'Invalid plural item name';
					}

					if (!empty($data['error']))
					{
						break;
					}

					$value = filter_input(INPUT_POST, 'item_value', FILTER_VALIDATE_INT, $options) ?? 0;
					$quantity = filter_input(INPUT_POST, 'item_quantity', FILTER_VALIDATE_INT, $options) ?? 0;
					$weight = filter_input(INPUT_POST, 'item_weight', FILTER_VALIDATE_INT, $options) ?? 0;

					$itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT, $options);
					if (!empty($itemId))
					{
						ItemsModel::updateItem($itemId, $name, $nameSingle, $namePlural, $value, $quantity, $weight);
					}
					else
					{
						$itemId = ItemsModel::createItem($name, $nameSingle, $namePlural, $value, $quantity, $weight);
					}

					$quantities = filter_input(INPUT_POST, 'ingredient_quantity', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
					$deletedItems = filter_input(INPUT_POST, 'delete_ingredient', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
					$recipe = array_diff_key($quantities, $deletedItems);
					ItemsModel::setRecipe($itemId, $recipe);

					header('Location: /admin/items');
					return;
				case 'delete':
					if (filter_has_var(INPUT_POST, 'confirm_delete'))
					{
						$itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT, $options);
						ItemsModel::deleteItem($itemId);

					}

					header('Location: /admin/items');
					return;
			}
		}

		switch ($page)
		{
			case 'add':
				$data['action'] = '/admin/items/add';
				$data['item'] = [
					'name'			=> '',
					'nameSingle'	=> '',
					'namePlural'	=> '',
					'value'			=> '',
					'quantity'		=> '',
					'weight'		=> '',
				];
				break;
			case 'edit':
				$data['action'] = '/admin/items/edit/' . $slug;
			case 'delete':
				$data['action'] = '/admin/items/delete/' . $slug;
				$item = ItemsModel::getItemBySlug($slug);
				if (!$item)
				{
					header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
					echo '404 Not Found';
					return;
				}

				$data['action'] = sprintf('/admin/items/%s/%s', $page, $slug);

				$data['item'] = [
					'id'			=> $item['id'],
					'name'			=> htmlspecialchars($item['name']),
					'nameSingle'	=> htmlspecialchars($item['nameSingle']),
					'namePlural'	=> htmlspecialchars($item['namePlural']),
					'value'			=> $item['value'],
					'quantity'		=> $item['quantity'],
					'weight'		=> $item['weight'],
				];
				break;
			default:
				$items = ItemsModel::getItems();
				$data['items'] = [];
				foreach ($items as $item)
				{
					$data['items'][] = [
						'slug'		=> $item['slug'],
						'name'		=> htmlspecialchars($item['name']),
						'value'		=> $item['value'],
						'quantity'	=> $item['quantity'],
					];
				}
		}

		if ($page === 'add' || $page === 'edit')
		{
			$data['recipe'] = [];

			$items = ItemsModel::getItems();
			foreach ($items as $item)
			{
				if ($item['slug'] === $slug)
				{
					continue;
				}

				$data['items'][$item['id']] = [
					'id'	=> $item['id'],
					'name'	=> htmlspecialchars($item['name']),
				];
			}

			if (filter_has_var(INPUT_POST, 'update_ingredients'))
			{
				$itemQuantities = filter_input(INPUT_POST, 'ingredient_quantity', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
				$deletedItems = filter_input(INPUT_POST, 'delete_ingredient', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];
				$addedItems = filter_input(INPUT_POST, 'add_ingredient', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY) ?? [];

				foreach ($data['items'] as $item)
				{
					if (isset($deletedItems[$item['id']]))
					{
						continue;
					}

					if (in_array($item['id'], $addedItems) || (isset($itemQuantities[$item['id']]) && $itemQuantities[$item['id']] > 0))
					{
						$item['quantity'] = $itemQuantities[$item['id']] ?? 1;
						$data['recipe'][] = $item;
						unset($data['items'][$item['id']]);
					}
				}
			}
			else if (isset($data['item']['id']))
			{
				$recipe = ItemsModel::getRecipe($data['item']['id']);
				foreach ($recipe as $item)
				{
					$data['recipe'][] = [
						'id'		=> $item['itemId'],
						'name'		=> htmlspecialchars($item['itemName']),
						'quantity'	=> $item['quantity'],
					];
					unset($data['items'][$item['itemId']]);
				}
			}
		}

		$this->showTemplate('admin/items', $data);
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
