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
		$itemId = $itemName = $value = null;

		if ($stmt = self::db()->prepare("SELECT id, nameSingle, value FROM items WHERE weight > 0 ORDER BY -LOG(RAND()) / weight LIMIT 1;"))
		{
			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $value);
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

			return compact('itemId', 'itemName', 'value');
		}

		return false;
	}

	/**
	 * Buys an item for a user. This searches the store for an item matching
	 * the description and adds it to the user's inventory..
	 *
	 * @param string $user   The username of the user buying the item
	 * @param int    $itemId The ID of the item to buy
	 *
	 * @return array|boolean Array containing the user and value of the item
	 *                       bought, or false on failure
	 */
	public static function buy(string $user, int $itemId)
	{
		if ($stmt = self::db()->prepare("UPDATE items SET quantity = quantity - 1 WHERE quantity > 0 AND id = ?;"))
		{
			$stmt->bind_param('i', $itemId);
			$stmt->execute();
			$valid = $stmt->affected_rows > 0;
			$stmt->close();
		}

		return $valid && self::giveItem($user, $itemId);
	}

	/**
	 * Sells an item for a user. This searches a user inventory for an item
	 * matching the description and deletes it.
	 *
	 * @param string $user   The username of the user selling the item
	 * @param int    $itemId The ID of the item to sell
	 *
	 * @return array|boolean Array containing the user and value of the item
	 *                       sold, or false on failure
	 */
	public static function sell(string $user, int $itemId)
	{
		if ($stmt = self::db()->prepare("SELECT inventory.id FROM inventory LEFT JOIN items ON items.id = inventory.item WHERE inventory.user = ? AND items.id = ? LIMIT 1;"))
		{
			$stmt->bind_param('si', $user, $itemId);
			$stmt->execute();
			$stmt->bind_result($inventoryId);
			$valid = $stmt->fetch();
			$stmt->close();

			if (!$inventoryId)
			{
				return false;
			}

			if ($stmt = self::db()->prepare("DELETE FROM inventory WHERE id = ?;"))
			{
				$stmt->bind_param('i', $inventoryId);
				$stmt->execute();
				$stmt->close();

				return true;
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
		if ($stmt = self::db()->prepare("SELECT id, name, value, quantity FROM items WHERE quantity > 0 ORDER BY name ASC;"))
		{
			$store = [];

			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $value, $quantity);

			while ($stmt->fetch())
			{
				$store[] = compact('itemId', 'itemName', 'value', 'quantity');
			}

			$stmt->close();

			return $store;
		}

		return false;
	}

	/**
	 * Gets all the crafting recipes.
	 *
	 * @return array|boolean Array containing all of the recipes in the
	 *                       database, or false on failure
	 */
	public static function getRecipes()
	{
		if ($stmt = self::db()->prepare("SELECT item.name, ingredient.name, recipe.quantity FROM recipe LEFT JOIN items item ON item.id = recipe.item LEFT JOIN items ingredient ON ingredient.id = recipe.ingredient ORDER BY item.name ASC, ingredient.name ASC;"))
		{
			$recipes = [];

			$stmt->execute();
			$stmt->bind_result($itemName, $ingredient, $quantity);

			while ($stmt->fetch())
			{
				$recipes[$itemName][] = compact('ingredient', 'quantity');
			}

			return $recipes;
		}

		return false;
	}

	/**
	 * Gets a crafting recipe for the requested item.
	 *
	 * @param int $itemId The ID of the requested item
	 *
	 * @return array|boolean The ingredients or false if one doesn't exist
	 */
	public static function getRecipe(int $itemId)
	{
		$recipe = [];

		if ($stmt = self::db()->prepare("SELECT items.id, items.name, recipe.quantity FROM recipe LEFT JOIN items ON items.id = recipe.ingredient WHERE recipe.item = ?;"))
		{
			$stmt->bind_param('i', $itemId);
			$stmt->execute();
			$stmt->bind_result($itemId, $itemName, $quantity);

			while ($stmt->fetch())
			{
				$recipe[] = compact('itemId', 'itemName', 'quantity');
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
	 * Set the crafting recipe for an item.
	 *
	 * @param int   $itemId      The ID of the item for which to set the recipe
	 * @param array $ingredients Array of ingredient item quantities
	 */
	public static function setRecipe(int $itemId, array $ingredients)
	{
		if ($stmt = self::db()->prepare('DELETE FROM recipe WHERE item = ?;'))
		{
			$stmt->bind_param('i', $itemId);
			$stmt->execute();
			$stmt->close();
		}

		if ($stmt = self::db()->prepare('INSERT INTO recipe (item, ingredient, quantity) VALUES (?, ?, ?);'))
		{
			$stmt->bind_param('iii', $itemId, $ingredient, $quantity);
			foreach ($ingredients as $ingredient => $quantity)
			{
				if ($quantity < 1)
				{
					continue;
				}

				$stmt->execute();
			}
			$stmt->close();
		}
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
		$sql = "SELECT inventory.user, items.id, items.name, items.value, COUNT(*) FROM inventory LEFT JOIN items ON items.id = inventory.item ";
		if ($user)
		{
			$sql .= "WHERE inventory.user = ? ";
		}
		$sql .= "GROUP BY inventory.user, items.name, items.value ORDER BY inventory.user ASC, items.name ASC;";

		if ($stmt = self::db()->prepare($sql))
		{
			$inventory = [];

			if ($user)
			{
				$stmt->bind_param('s', $user);
			}
			$stmt->execute();
			$stmt->bind_result($user, $itemId, $itemName, $value, $quantity);

			while ($stmt->fetch())
			{
				$inventory[] = compact('user', 'itemId', 'itemName', 'quantity', 'value');
			}

			$stmt->close();

			return $inventory;
		}

		return false;
	}

	/**
	 * Give an item to a user.
	 *
	 * @param string $user   The username of the user to receive the item
	 * @param int    $itemId The ID of the item to give
	 *
	 * @return boolean True on success, false on failure
	 */
	public static function giveItem(string $user, int $itemId)
	{
		if ($stmt = self::db()->prepare("INSERT INTO inventory (user, item) VALUES (?, ?);"))
		{
			$stmt->bind_param('si', $user, $itemId);
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

	/**
	 * Gets information about an item.
	 *
	 * @param string $item The name of the item
	 *
	 * @return array|boolean Array containing information about the item, or
	 *                       false if the item does not exist
	 */
	public static function findItem(string $item)
	{
		if ($stmt = self::db()->prepare("SELECT id, slug, name, nameSingle, namePlural, value, quantity FROM items WHERE name = ? OR nameSingle = ? OR namePlural = ?;"))
		{
			$stmt->bind_param('sss', $item, $item, $item);
			$stmt->execute();
			$stmt->bind_result($id, $slug, $name, $nameSingle, $namePlural, $value, $quantity);
			$stmt->fetch();
			$stmt->close();
		}

		if ($id)
		{
			return compact('id', 'slug', 'name', 'nameSingle', 'namePlural', 'value', 'quantity');
		}

		return false;
	}

	/**
	 * Get an item from an item ID.
	 *
	 * @param int $itemId The ID of the item to get
	 *
	 * @return array|boolean The item data, or false if the item is not found
	 */
	public static function getItemById(int $itemId)
	{
		return self::getItem('id', 'i', $itemId);
	}

	/**
	 * Get an item from an item slug.
	 *
	 * @param string $slug The slug of the item to get
	 *
	 * @return array|boolean The item data, or false if the item is not found
	 */
	public static function getItemBySlug(string $slug)
	{
		return self::getItem('slug', 's', $slug);
	}

	/**
	 * Get all of the items in the database.
	 *
	 * @return array|boolean The data for all items, or false on failure
	 */
	public static function getItems()
	{
		if ($stmt = self::db()->prepare('SELECT id, slug, name, value, quantity FROM items ORDER BY name ASC;'))
		{
			$items = [];

			$stmt->execute();
			$stmt->bind_result($id, $slug, $name, $value, $quantity);

			while ($stmt->fetch())
			{
				$items[] = compact('id', 'slug', 'name', 'value', 'quantity');
			}

			$stmt->close();

			return $items;
		}

		return false;
	}

	/**
	 * Create a new item.
	 *
	 * @param string $name       The base name of the item
	 * @param string $nameSingle The singular phrase for the item
	 * @param string $namePlural The plural phrase for the item
	 * @param int    $value      The point value of the item
	 * @param int    $quantity   The quantity of the item in the store
	 * @param int    $weight     The weight of the item for the randomizer
	 *
	 * @return int The ID of the item
	 */
	public static function createItem(string $name, string $nameSingle, string $namePlural, int $value, int $quantity, int $weight)
	{
		if ($stmt = self::db()->prepare('INSERT INTO items (slug, name, nameSingle, namePlural, value, quantity, weight) VALUES (?, ?, ?, ?, ?, ?, ?);'))
		{
			$slug = self::getSlug($name);
			$stmt->bind_param('ssssiii', $slug, $name, $nameSingle, $namePlural, $value, $quantity, $weight);
			$success = $stmt->execute();
			$stmt->close();

			if ($success)
			{
				return self::db()->insert_id;
			}
		}

		return 0;
	}

	/**
	 * Update the details of an item.
	 *
	 * @param int    $itemId     The ID of the item
	 * @param string $name       The base name of the item
	 * @param string $nameSingle The singular phrase for the item
	 * @param string $namePlural The plural phrase for the item
	 * @param int    $value      The point value of the item
	 * @param int    $quantity   The quantity of the item in the store
	 * @param int    $weight     The weight of the item for the randomizer
	 */
	public static function updateItem(int $itemId, string $name, string $nameSingle, string $namePlural, int $value, int $quantity, int $weight)
	{
		if ($stmt = self::db()->prepare('UPDATE items SET slug = ?, name = ?, nameSingle = ?, namePlural = ?, value = ?, quantity = ?, weight = ? WHERE id = ?;'))
		{
			$newSlug = self::getSlug($name);
			$stmt->bind_param('ssssiiii', $newSlug, $name, $nameSingle, $namePlural, $value, $quantity, $weight, $itemId);
			$stmt->execute();
			$stmt->close();
		}
	}

	/**
	 * Delete an item.
	 *
	 * @param int $itemId The ID of the item
	 */
	public static function deleteItem(int $itemId)
	{
		if ($stmt = self::db()->prepare('DELETE FROM items WHERE id = ?;'))
		{
			$stmt->bind_param('i', $itemId);
			$stmt->execute();
			$stmt->close();
		}
	}

	/**
	 * Get an item.
	 *
	 * @param string $varName  The name of the database column
	 * @param string $varType  The type of database column
	 * @param mixed  $variable The value of the database column
	 *
	 * @return array|boolean The item data, or false if the item is not found
	 */
	protected static function getItem(string $varName, string $varType, $variable)
	{
		$sql = sprintf('SELECT id, slug, name, nameSingle, namePlural, value, quantity, weight FROM items WHERE %s = ?;', $varName);
		if ($stmt = self::db()->prepare($sql))
		{
			$stmt->bind_param($varType, $variable);
			$stmt->execute();
			$stmt->bind_result($id, $slug, $name, $nameSingle, $namePlural, $value, $quantity, $weight);
			$stmt->fetch();
			$stmt->close();
		}

		if ($id)
		{
			return compact('id', 'slug', 'name', 'nameSingle', 'namePlural', 'value', 'quantity', 'weight');
		}

		return false;
	}

	/**
	 * Get the slug of an item based on its name.
	 *
	 * @param string $itemName The name of the item
	 *
	 * @return string The slug for the item
	 */
	protected static function getSlug(string $itemName)
	{
		return preg_replace('/[^a-z0-9\\-]/', '', strtolower(str_replace(' ', '-', $itemName)));
	}
}
