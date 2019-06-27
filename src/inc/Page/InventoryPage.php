<?php

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\ItemsModel;

class InventoryPage extends Page
{
    public function run(array $params)
    {
    	$data = [
    		'inventory'	=> [],
    		'user'		=> htmlspecialchars($params[0] ?? 'All Users'),
    	];

    	$inventory = ItemsModel::getInventory($params[0]);
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
    				'item'		=> htmlspecialchars($item['item']),
    				'modifier'	=> htmlspecialchars($item['modifier']),
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
