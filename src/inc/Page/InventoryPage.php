<?php

namespace StevoTVRBot\Page;

use StevoTVRBot\Model\ItemsModel;

class InventoryPage extends Page
{
    public function run()
    {
    	$inventory = ItemsModel::getInventory();
    	if (is_array($inventory))
    	{
    		foreach ($inventory as &$user)
    		{
    			foreach ($user as &$item)
    			{
    				$item['description'] = htmlspecialchars($item['description']);
    			}
    		}

    		$this->showTemplate('inventory', ['inventory' => $inventory]);
    	}
    }
}
