<?php require __DIR__ . '/../error.php'; ?>
		<form action="<?php echo $action; ?>" method="POST">
			<input type="hidden" id="action" name="action" value="update">
<?php if ($page === 'edit'): ?>
			<input type="hidden" id="tip_id" name="tip_id" value="<?php echo $tipId; ?>">
<?php endif; ?>
			<section>
				<h3>Edit Tip</h3>
				<table cellspacing="0">
<?php if ($page === 'edit'): ?>
					<tr>
						<td>Created:</td>
						<td><?php echo $tip['time']; ?></td>
					</tr>
<?php endif; ?>
					<tr>
						<td><label for="tip_user">User:</label></td>
						<td><input type="text" id="tip_user" name="tip_user" value="<?php echo $tip['user']; ?>"></td>
					</tr>
					<tr>
						<td><label for="tip_message">Message:</label></td>
						<td><input type="text" id="tip_message" name="tip_message" value="<?php echo $tip['message']; ?>"></td>
					</tr>
					<tr>
						<td><label for="tip_status">Status:</label></td>
						<td>
							<select id="tip_status" name="tip_status">
<?php foreach ($statusOptions as $id => $status): ?>
								<option value="<?php echo $id; ?>"<?php if ($tip['status'] === $id): ?> selected<?php endif; ?>><?php echo $status; ?>></option>
<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<footer>
					<input type="submit" name="update" value="Update">&nbsp;<input type="reset" name="reset" value="Reset">
				</footer>
			</section>
		</form>
