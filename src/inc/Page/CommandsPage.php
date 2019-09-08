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

/**
 * Handler for the commands page, which lists the available chat commands.
 */
class CommandsPage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$data = [
			'commands'	=> [
				[
					'name'		=> 'Public',
					'commands'	=> [],
				],
				[
					'name'		=> 'Subscriber',
					'commands'	=> [],
				],
			],
		];

		$commands = CommandsModel::getCommands();
		if (is_array($commands))
		{
			foreach ($commands as $command)
			{
				$data['commands'][$command['level']]['commands'][] = [
					'name'			=> htmlspecialchars($command['command'] . ' ' . $command['arguments']),
					'description'	=> htmlspecialchars($command['description']),
				];
			}

			$this->showTemplate('commands', $data);
		}
	}
}
