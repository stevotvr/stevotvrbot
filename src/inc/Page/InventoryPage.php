<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\ItemsModel;
use StevoTVRBot\Model\SettingsModel;

/**
 * Handler for the inventory page, which displays items in user inventories.
 */
class InventoryPage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$data = [
			'pointsName'	=> htmlspecialchars(SettingsModel::getPointsName()),
			'inventory'		=> [],
			'user'			=> htmlspecialchars($params[0] ?? 'All Users'),
		];

		$inventory = ItemsModel::getInventory($params[0] ?? null);
		if (is_array($inventory))
		{
			foreach ($inventory as $item)
			{
				if (!isset($data['inventory'][$item['user']]))
				{
					$data['inventory'][$item['user']] = [
						'total'	=> [
							'items'	=> 0,
							'value'	=> 0,
						],
					];
				}

				$data['inventory'][$item['user']]['items'][] = [
					'item'		=> htmlspecialchars($item['itemName']),
					'quantity'	=> $item['quantity'],
					'value'		=> $item['value'],
				];
				$data['inventory'][$item['user']]['total']['items'] += $item['quantity'];
				$data['inventory'][$item['user']]['total']['value'] += $item['value'];
			}

			$this->showTemplate('inventory', $data);
		}
	}
}
