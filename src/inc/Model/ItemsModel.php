<?php

namespace StevoTVRBot\Model;

class ItemsModel extends Model
{
	public static function find(string $user)
	{
		$itemId = $itemName = $itemValue = $modId = $modDesc = $modValue = null;

        if ($stmt = self::db()->prepare("SELECT id, item, value FROM items ORDER BY -LOG(RAND()) / weight LIMIT 1;"))
        {
            $stmt->execute();
            $stmt->bind_result($itemId, $itemName, $itemValue);
            $stmt->fetch();
            $stmt->close();
        }

        if ($stmt = self::db()->prepare("SELECT id, description, value FROM modifiers ORDER BY -LOG(RAND()) / weight LIMIT 1;"))
        {
            $stmt->execute();
            $stmt->bind_result($modId, $modDesc, $modValue);
            $stmt->fetch();
            $stmt->close();
        }

        if (!$itemId || !$modId)
        {
        	return false;
        }

        $description = $modDesc . ' ' . $itemName;
        $value = ($modValue / 100) * $itemValue;

        if ($stmt = self::db()->prepare("INSERT INTO inventory (user, modifier, item, value, description) VALUES (?, ?, ?, ?, ?);"))
        {
            $stmt->bind_param('siiis', $user, $modId, $itemId, $value, $description);
            $stmt->execute();
            $stmt->close();

            return [
            	'user'			=> $user,
            	'description'	=> $description,
            	'value'			=> $value,
            ];
        }

        return false;
 	}

 	public static function sell(string $user, string $item)
 	{
        if ($stmt = self::db()->prepare("SELECT id, value FROM inventory WHERE user = ? AND description = ? LIMIT 1;"))
        {
            $stmt->bind_param('ss', $user, $item);
            $stmt->execute();
            $stmt->bind_result($itemId, $value);
            $valid = $stmt->fetch();
            $stmt->close();

            if (!$value)
            {
            	return false;
            }

	        if ($stmt = self::db()->prepare("DELETE FROM inventory WHERE id = ?;"))
	        {
	            $stmt->bind_param('i', $itemId);
	            $stmt->execute();
	            $stmt->close();

	            return [
	            	'user'	=> $user,
	            	'value'	=> $value,
	            ];
	        }
        }

        return false;
 	}

 	public function getInventory()
 	{
 		if ($stmt = self::db()->prepare("SELECT inventory.user, items.item, modifiers.description, inventory.value, COUNT(*) FROM inventory LEFT JOIN items ON items.id = inventory.item LEFT JOIN modifiers ON modifiers.id = inventory.modifier GROUP BY inventory.user, items.item, modifiers.description, inventory.value ORDER BY inventory.user ASC, items.item ASC;"))
 		{
 			$inventory = [];

 			$stmt->execute();
 			$stmt->bind_result($user, $item, $modifier, $value, $quantity);

 			while ($stmt->fetch())
 			{
 				$inventory[] = [
 					'user'		=> $user,
 					'item'		=> $item,
 					'modifier'	=> $modifier,
 					'quantity'	=> $quantity,
 					'value'		=> $value,
 				];
 			}

 			$stmt->close();

 			return $inventory;
 		}

 		return false;
 	}
}
