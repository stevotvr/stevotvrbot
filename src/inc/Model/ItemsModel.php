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
	 * @param string $user The username of the user finding the item
	 *
	 * @return array|boolean Array describing the found item, or false on
	 *                       failure
	 */
	public static function find(string $user)
	{
		$itemId = $itemName = $itemValue = null;

		if ($stmt = self::db()->prepare("SELECT id, item, value FROM items WHERE weight > 0 ORDER BY -LOG(RAND()) / weight LIMIT 1;"))
		{
			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $itemValue);
			$stmt->fetch();
			$stmt->close();
		}

		if (!$itemId)
		{
			return false;
		}

		if ($stmt = self::db()->prepare("INSERT INTO inventory (user, item) VALUES (?, ?);"))
		{
			$stmt->bind_param('si', $user, $itemId);
			$stmt->execute();
			$stmt->close();

			return [
				'user'			=> $user,
				'description'	=> $itemName,
				'value'			=> $itemValue,
			];
		}

		return false;
	}

	/**
	 * Buys an item for a user. This searches the store for an item matching
	 * the description and adds it to the user's inventory..
	 *
	 * @param string $user The username of the user buying the item
	 * @param string $item The description of the item to buy
	 *
	 * @return array|boolean Array containing the user and value of the item
	 *                       bought, or false on failure
	 */
	public static function buy(string $user, string $item)
	{
		if ($stmt = self::db()->prepare("UPDATE items SET quantity = quantity - 1 WHERE quantity > 0 AND item = ?;"))
		{
			$stmt->bind_param('s', $item);
			$stmt->execute();
			$valid = $stmt->affected_rows > 0;
			$stmt->close();
		}

		return $valid && self::giveItem($user, $item);
	}

	/**
	 * Sells an item for a user. This searches a user inventory for an item
	 * matching the description and deletes it.
	 *
	 * @param string $user The username of the user selling the item
	 * @param string $item The description of the item to sell
	 *
	 * @return array|boolean Array containing the user and value of the item
	 *                       sold, or false on failure
	 */
	public static function sell(string $user, string $item)
	{
		if ($stmt = self::db()->prepare("SELECT inventory.id, items.id, items.value FROM inventory LEFT JOIN items ON items.id = inventory.item WHERE inventory.user = ? AND items.item = ? LIMIT 1;"))
		{
			$stmt->bind_param('ss', $user, $item);
			$stmt->execute();
			$stmt->bind_result($inventoryId, $itemId, $value);
			$valid = $stmt->fetch();
			$stmt->close();

			if (!$value)
			{
				return false;
			}

			if ($stmt = self::db()->prepare("DELETE FROM inventory WHERE id = ?;"))
			{
				$stmt->bind_param('i', $inventoryId);
				$stmt->execute();
				$stmt->close();

				return [
					'user'		=> $user,
					'itemId'	=> $itemId,
					'value'		=> $value,
				];
			}
		}

		return false;
	}

	/**
	 * Gets all of the items available from the store.
	 *
	 * @return array|boolean Array containing information on all the items
	 *                       available from the store, or false on failure
	 */
	public static function getStore()
	{
		if ($stmt = self::db()->prepare("SELECT id, item, value, quantity FROM items WHERE quantity > 0 ORDER BY item ASC;"))
		{
			$store = [];

			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $itemValue, $itemQuantity);

			while ($stmt->fetch())
			{
				$store[] = [
					'itemId'	=> $itemId,
					'item'		=> $itemName,
					'value'		=> $itemValue,
					'quantity'	=> $itemQuantity,
				];
			}

			$stmt->close();

			return $store;
		}

		return false;
	}

	/**
	 * Gets information about an item in the store.
	 *
	 * @param string $item The name of the item
	 *
	 * @return array|boolean Array containing the value and quantity of the
	 *                       requested item, or false if the item doesn't exist
	 */
	public static function getStoreItem(string $item)
	{
		$itemId = $itemName = $itemValue = $itemQuantity = null;

		if ($stmt = self::db()->prepare("SELECT id, item, value, quantity FROM items WHERE item = ?;"))
		{
			$stmt->bind_param('s', $item);
			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $itemValue, $itemQuantity);
			$stmt->fetch();
			$stmt->close();
		}

		if (!$itemId)
		{
			return false;
		}

		return [
			'itemId'		=> $itemId,
			'description'	=> $itemName,
			'value'			=> $itemValue,
			'quantity'		=> $itemQuantity,
		];
	}

	/**
	 * Gets all the crafting recipes.
	 *
	 * @return array|boolean Array containing all of the recipes in the
	 *                       database, or false on failure
	 */
	public static function getRecipes()
	{
		if ($stmt = self::db()->prepare("SELECT item.item, ingredient.item, recipe.quantity FROM recipe LEFT JOIN items item ON item.id = recipe.item LEFT JOIN items ingredient ON ingredient.id = recipe.ingredient ORDER BY item.item ASC, ingredient.item ASC;"))
		{
			$recipes = [];

			$stmt->execute();
			$stmt->bind_result($itemName, $ingredient, $quantity);

			while ($stmt->fetch())
			{
				$recipes[$itemName][] = [
					'ingredient'	=> $ingredient,
					'quantity'		=> $quantity,
				];
			}

			return $recipes;
		}

		return false;
	}

	/**
	 * Gets a crafting recipe for the requested item.
	 *
	 * @param string $item The name of the requested item
	 *
	 * @return array|boolean The ingredients or false if one doesn't exist
	 */
	public static function getRecipe(string $item)
	{
		$recipe = [];

		if ($stmt = self::db()->prepare("SELECT items.id, items.item, recipe.quantity FROM recipe LEFT JOIN items ON items.id = recipe.ingredient WHERE recipe.item = (SELECT id FROM items WHERE item = ?);"))
		{
			$stmt->bind_param('s', $item);
			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $quantity);

			while ($stmt->fetch())
			{
				$recipe[] = [
					'itemId'	=> $itemId,
					'item'		=> $itemName,
					'quantity'	=> $quantity,
				];
			}

			$stmt->close();
		}

		if (empty($recipe))
		{
			return false;
		}

		return $recipe;
	}

	/**
	 * Get the inventory of found items.
	 *
	 * @param string|null $user The user by which to limit the search, or null
	 *                          to get all inventories
	 *
	 * @return array|boolean Array containing inventory data, or false on
	 *                       failure
	 */
	public static function getInventory(string $user = null)
	{
		$sql = "SELECT inventory.user, items.id, items.item, items.value, COUNT(*) FROM inventory LEFT JOIN items ON items.id = inventory.item ";
		if ($user)
		{
			$sql .= "WHERE inventory.user = ? ";
		}
		$sql .= "GROUP BY inventory.user, items.item, items.value ORDER BY inventory.user ASC, items.item ASC;";

		if ($stmt = self::db()->prepare($sql))
		{
			$inventory = [];

			if ($user)
			{
				$stmt->bind_param('s', $user);
			}
			$stmt->execute();
			$stmt->bind_result($user, $itemId, $item, $value, $quantity);

			while ($stmt->fetch())
			{
				$inventory[] = [
					'user'		=> $user,
					'itemId'	=> $itemId,
					'item'		=> $item,
					'quantity'	=> $quantity,
					'value'		=> $value,
				];
			}

			$stmt->close();

			return $inventory;
		}

		return false;
	}

	/**
	 * Give an item to a user.
	 *
	 * @param string $user The username of the user to receive the item
	 * @param string $item The name of the item to give
	 *
	 * @return boolean True on success, false on failure
	 */
	public static function giveItem(string $user, string $item)
	{
		if ($stmt = self::db()->prepare("INSERT INTO inventory (user, item) VALUES (?, (SELECT id FROM items WHERE item = ?));"))
		{
			$stmt->bind_param('ss', $user, $item);
			$stmt->execute();
			$stmt->close();

			return true;
		}

		return false;
	}

	/**
	 * Removes items from a user's inventory.
	 *
	 * @param string $user  The username of the user
	 * @param array  $items The items to remove
	 */
	public static function takeItems(string $user, array $items)
	{
		if ($stmt = self::db()->prepare("DELETE FROM inventory WHERE user = ? AND item = ? ORDER BY time ASC LIMIT ?;"))
		{
			foreach ($items as $item)
			{
				$stmt->bind_param('sii', $user, $item['itemId'], $item['quantity']);
				$stmt->execute();
			}

			$stmt->close();
		}
	}

	/**
	 * Adds an item to the store.
	 *
	 * @param int $itemId The ID of the item to add
	 */
	public static function addToStore(int $itemId)
	{
		if ($stmt = self::db()->prepare("UPDATE items SET quantity = quantity + 1 WHERE id = ?;"))
		{
			$stmt->bind_param('i', $itemId);
			$stmt->execute();
			$stmt->close();

			return true;
		}

		return false;
	}
}
