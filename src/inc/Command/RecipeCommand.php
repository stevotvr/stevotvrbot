<?php

/**
 * StevoTVRBot. Supplies a custom API for the StreamElements chat bot on the
 * StevoTVR Twitch channel.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license https://github.com/stevotvr/stevotvrbot/blob/master/LICENSE MIT License
 */

namespace StevoTVRBot\Command;

use StevoTVRBot\Model\ItemsModel;

/**
 * Handler for the !recipe command. This command finds the recipe for the
 * requested item if it exists and returns the list of required ingredients.
 *
 * Usage: !recipe <item>
 * Returns: Recipe to craft <item>: <ingredients>
 */
class RecipeCommand extends Command
{
	/**
	 * @inheritDoc
	 */
	protected function exec(string $args, string $user = null)
	{
		if (!$args)
		{
			echo 'Usage: !recipe <item>';
			return;
		}

		$itemInfo = ItemsModel::getItem($args);
		if (!$itemInfo)
		{
			printf('Unknown item: %s', $args);
			return;
		}

		$recipe = ItemsModel::getRecipe($itemInfo['id']);
		if (!$recipe)
		{
			printf('No recipe found for %s', $itemInfo['namePlural']);
			return;
		}

		$ingredients = [];
		foreach ($recipe as $ingredient)
		{
			$ingredients[] = $ingredient['quantity'] . 'x ' . $ingredient['itemName'];
		}

		if (count($ingredients) > 1)
		{
			$ingredients[] = 'and ' . array_pop($ingredients);
		}

		printf('Recipe to craft %s: %s', $itemInfo['nameSingle'], implode(count($ingredients) > 2 ? ', ' : ' ', $ingredients));
	}
}
