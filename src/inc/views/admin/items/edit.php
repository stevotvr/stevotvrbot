<?php require __DIR__ . '/../error.php'; ?>
        <form action="<?php echo $action; ?>" method="POST">
			<input type="hidden" id="action" name="action" value="update">
<?php if ($page === 'edit'): ?>
			<input type="hidden" id="item_id" name="item_id" value="<?php echo $item['id']; ?>">
<?php endif; ?>
            <section>
				<h3>Edit Item</h3>
                <table cellspacing="0">
                    <tr>
                        <td><label for="item_name">Item Name:</label></td>
                        <td><input type="text" id="item_name" name="item_name" value="<?php echo $item['name']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="item_name_single">Item Name (Single):</label></td>
                        <td><input type="text" id="item_name_single" name="item_name_single" value="<?php echo $item['nameSingle']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="item_name_plural">Item Name (Plural):</label></td>
                        <td><input type="text" id="item_name_plural" name="item_name_plural" value="<?php echo $item['namePlural']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="item_value">Item Value:</label></td>
                        <td><input type="number" id="item_value" name="item_value" value="<?php echo $item['value']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="item_quantity">Item Quantity:</label></td>
                        <td><input type="number" id="item_quantity" name="item_quantity" value="<?php echo $item['quantity']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="item_weight">Item Weight:</label></td>
                        <td><input type="number" id="item_weight" name="item_weight" value="<?php echo $item['weight']; ?>"></td>
                    </tr>
				</table>
				<h3>Crafting Recipe</h3>
				<table>
					<tr>
						<th>Ingredient</th>
						<th>Quantity</th>
						<th>Delete</th>
					</tr>
<?php foreach ($recipe as $ingredient): ?>
					<tr>
						<td><?php echo $ingredient['name']; ?></td>
						<td><input type="number" name="ingredient_quantity[<?php echo $ingredient['id']; ?>]" value="<?php echo $ingredient['quantity']; ?>"></td>
						<td><input type="checkbox" name="delete_ingredient[<?php echo $ingredient['id']; ?>]"></td>
					</tr>
<?php endforeach; ?>
				</table>
				<select name="add_ingredient[]" id="add_ingredient" multiple size="10">
<?php foreach ($items as $item): ?>
					<option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
<?php endforeach; ?>
				</select>
				<input type="submit" name="update_ingredients" value="Update Recipe">
                <footer>
                    <input type="submit" name="save_item" value="Save">&nbsp;<input type="reset" name="reset" value="Reset">
                </footer>
			</section>
        </form>
