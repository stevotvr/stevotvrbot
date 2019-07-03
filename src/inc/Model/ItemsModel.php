<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Model;

/**
 * Model representing the items database.
 */
class ItemsModel extends Model
{
	/**
	 * Find an item for a user. This fetches a weighted random item/modifier
	 * combination, calculates its value, and stores it in a user inventory.
	 *
	 * @param  string $user The username of the user finding the item
	 *
	 * @return array|boolean Array describing the found item, or false on
	 *                       failure
	 */
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

 	/**
 	 * Sells an item for a user. This searches a user inventory for an item
 	 * matching the description and deletes it.
 	 *
 	 * @param  string $user The username of the user selling the item
 	 * @param  string $item The description of the item to sell
 	 *
 	 * @return array|boolean Array containing the user and value of the item
 	 *                       sold, or false on failure
 	 */
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

 	/**
 	 * Get the inventory of found items.
 	 *
 	 * @param  string|null $user The user by which to limit the search, or null
 	 *                           to get all inventories
 	 *
 	 * @return array|boolean Array containing inventory data, or false on
 	 *                       failure
 	 */
 	public function getInventory(string $user = null)
 	{
 		$sql = "SELECT inventory.user, items.item, modifiers.description, inventory.value, COUNT(*) FROM inventory LEFT JOIN items ON items.id = inventory.item LEFT JOIN modifiers ON modifiers.id = inventory.modifier ";
 		if ($user)
 		{
 			$sql .= "WHERE inventory.user = ? ";
 		}
 		$sql .= "GROUP BY inventory.user, items.item, modifiers.description, inventory.value ORDER BY inventory.user ASC, items.item ASC;";

 		if ($stmt = self::db()->prepare($sql))
 		{
 			$inventory = [];

	 		if ($user)
	 		{
	 			$stmt->bind_param('s', $user);
	 		}
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
