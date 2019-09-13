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

/**
 * Handler for the recipes page, which displays all of the crafting recipes.
 */
class RecipesPage extends Page
{
	/**
	 * @inheritDoc
	 */
	public function run(array $params)
	{
		$data = [
			'recipes'	=> [],
		];

		$recipes = ItemsModel::getRecipes();
		if (is_array($recipes))
		{
			foreach ($recipes as $item => $recipe)
			{
				$item = htmlspecialchars($item);
				$data['recipes'][$item] = [];

				foreach ($recipe as $ingredient)
				{
					$data['recipes'][$item][] = [
						'ingredient'	=> htmlspecialchars($ingredient['ingredient']),
						'quantity'		=> $ingredient['quantity'],
					];
				}
			}

			$this->showTemplate('recipes', $data);
		}
	}
}
