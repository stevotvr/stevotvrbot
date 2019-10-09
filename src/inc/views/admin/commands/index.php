<?php require __DIR__ . '/../nav.php'; ?>
	<section class="main" id="admin">
		<header>
			<h2><a href="/admin">Admin</a></h2>
		</header>
<?php require __DIR__ . '/../error.php'; ?>
		<form action="<?php echo $action; ?>" method="POST">
			<input type="hidden" name="action" value="update">
			<section>
				<table cellspacing="0">
					<tr>
						<th>Command</th>
						<th>Arguments</th>
						<th>Description</th>
						<th>Level</th>
						<th>Delete</th>
					</tr>
<?php foreach ($commands as $command): ?>
					<tr>
						<td>!<input type="text" name="command_name[<?php echo $command['id']; ?>]" value="<?php echo $command['name']; ?>"></td>
						<td><input type="text" name="command_arguments[<?php echo $command['id']; ?>]" value="<?php echo $command['arguments']; ?>"></td>
						<td><input type="text" name="command_description[<?php echo $command['id']; ?>]" value="<?php echo $command['description']; ?>"></td>
						<td>
							<select name="command_level[<?php echo $command['id']; ?>]">
<?php foreach ($levels as $id => $level): ?>
								<option value="<?php echo $id; ?>"<?php if ($command['level'] === $id): ?> selected<?php endif; ?>><?php echo $level; ?></option>
<?php endforeach; ?>
							</select>
						</td>
						<td><input type="checkbox" name="command_delete[<?php echo $command['id']; ?>]"></td>
					</tr>
<?php endforeach; ?>
				</table>
				<footer>
					<input type="submit" name="update" value="Update">&nbsp;<input type="reset" name="reset" value="Reset">
				</footer>
			</section>
		</form>
		<form action="<?php echo $action; ?>" method="POST">
			<input type="hidden" name="action" value="add">
			<section>
				<table cellspacing="0">
					<tr>
						<td><label for="command_name">Command:</label></td>
						<td>!<input type="text" id="command_name" name="command_name"></td>
					</tr>
					<tr>
						<td><label for="command_arguments">Arguments:</label></td>
						<td><input type="text" id="command_arguments" name="command_arguments"></td>
					</tr>
					<tr>
						<td><label for="command_description">Description:</label></td>
						<td><input type="text" id="command_description" name="command_description"></td>
					</tr>
					<tr>
						<td><label for="command_level">Level:</label></td>
						<td>
							<select name="command_level" id="command_level">
<?php foreach ($levels as $id => $level): ?>
								<option value="<?php echo $id; ?>"><?php echo $level; ?></option>
<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<footer>
					<input type="submit" name="add" value="Add Command">
				</footer>
			</section>
		</form>
	</section>
