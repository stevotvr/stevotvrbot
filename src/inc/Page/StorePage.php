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
 * Handler for the store page, which displays items available from the store.
 */
class StorePage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$data = [
			'pointsName'	=> htmlspecialchars(SettingsModel::getPointsName()),
			'store'			=> [],
		];

		$store = ItemsModel::getStore();
		if (is_array($store))
		{
			foreach ($store as $item)
			{
				$data['store'][] = [
					'item'		=> htmlspecialchars($item['item']),
					'quantity'	=> $item['quantity'],
					'value'		=> $item['value'],
				];
			}

			$this->showTemplate('store', $data);
		}
	}
}
