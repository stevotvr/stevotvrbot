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
						<th>Day</th>
						<th>Hour</th>
						<th>Minute</th>
						<th>Length</th>
						<th>Game</th>
						<th>Platform</th>
						<th>Active</th>
						<th>Delete</th>
					</tr>
<?php foreach ($schedule as $day): ?>
					<tr>
						<td>
							<select name="schedule_day[<?php echo $day['id']; ?>]">
<?php foreach ($weekDays as $id => $name): ?>
								<option value="<?php echo $id; ?>"<?php if ($day['day'] === $id): ?> selected<?php endif; ?>><?php echo $name; ?></option>
<?php endforeach; ?>
							</select>
						</td>
						<td><input type="number" min="0" max="23" name="schedule_hour[<?php echo $day['id']; ?>]" value="<?php echo $day['hour']; ?>"></td>
						<td><input type="number" min="0" max="59" name="schedule_minute[<?php echo $day['id']; ?>]" value="<?php echo $day['minute']; ?>"></td>
						<td><input type="number" min="0" name="schedule_length[<?php echo $day['id']; ?>]" value="<?php echo $day['length']; ?>"></td>
						<td><input type="text" name="schedule_game[<?php echo $day['id']; ?>]" value="<?php echo $day['game']; ?>"></td>
						<td><input type="text" name="schedule_platform[<?php echo $day['id']; ?>]" value="<?php echo $day['platform']; ?>"></td>
						<td><input type="checkbox" name="schedule_active[<?php echo $day['id']; ?>]"<?php if ($day['active']): ?> checked<?php endif; ?>></td>
						<td><input type="checkbox" name="schedule_delete[<?php echo $day['id']; ?>]"></td>
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
						<td><label for="schedule_day">Day:</label></td>
						<td>
							<select id="schedule_day" name="schedule_day">
	<?php foreach ($weekDays as $id => $name): ?>
								<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
	<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="schedule_hour">Hour:</label></td>
						<td><input type="number" id="schedule_hour" name="schedule_hour" min="0" max="23"></td>
					</tr>
					<tr>
						<td><label for="schedule_minute">Minute:</label></td>
						<td><input type="number" id="schedule_minute" name="schedule_minute" min="0" max="59"></td>
					</tr>
					<tr>
						<td><label for="schedule_length">Length:</label></td>
						<td><input type="number" id="schedule_length" name="schedule_length" min="0"></td>
					</tr>
					<tr>
						<td><label for="schedule_game">Game:</label></td>
						<td><input type="text" id="schedule_game" name="schedule_game"></td>
					</tr>
					<tr>
						<td><label for="schedule_platform">Platform:</label></td>
						<td><input type="text" id="schedule_platform" name="schedule_platform"></td>
					</tr>
					<tr>
						<td><label for="schedule_active">Active:</label></td>
						<td><input type="checkbox" id="schedule_active" name="schedule_active" checked></td>
					</tr>
				</table>
				<footer>
					<input type="submit" name="add" value="Add Item">
				</footer>
			</section>
		</form>
	</section>
