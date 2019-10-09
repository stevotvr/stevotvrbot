<?php require __DIR__ . '/../nav.php'; ?>
	<section class="main" id="admin">
		<header>
			<h2><a href="/admin">Admin</a></h2>
		</header>
<?php if ($page === 'add' || $page === 'edit'): ?>
<?php require 'edit.php'; ?>
<?php elseif ($page === 'delete'): ?>
<?php require 'delete.php'; ?>
<?php else: ?>
<?php require __DIR__ . '/../error.php'; ?>
		<section>
			<form action="<?php echo $action; ?>" method="POST">
				<input type="hidden" id="action" name="action" value="update_status">
				<h4>Active Tips</h4>
				<table cellspacing="0">
					<tr>
						<th>Created</th>
						<th>User</th>
						<th>Message</th>
						<th>Actions</th>
					</tr>
<?php foreach ($tips['APPROVED'] as $tip): ?>
					<tr>
						<td><?php echo $tip['time']; ?></td>
						<td><?php echo $tip['user']; ?></td>
						<td><?php echo $tip['message']; ?></td>
						<td>
							<a href="/admin/tips/edit/<?php echo $tip['id']; ?>">Edit</a>&nbsp;&bull;&nbsp;
							<a href="/admin/tips/delete/<?php echo $tip['id']; ?>">Delete</a>
						</td>
					</tr>
<?php endforeach; ?>
				</table>
				<h4>Pending Tips</h4>
				<table cellspacing="0">
					<tr>
						<th>Created</th>
						<th>User</th>
						<th>Message</th>
						<th>Actions</th>
						<th>Status</th>
					</tr>
<?php foreach ($tips['PENDING'] as $tip): ?>
					<tr>
						<td><?php echo $tip['time']; ?></td>
						<td><?php echo $tip['user']; ?></td>
						<td><?php echo $tip['message']; ?></td>
						<td>
							<a href="/admin/tips/edit/<?php echo $tip['id']; ?>">Edit</a>&nbsp;&bull;&nbsp;
							<a href="/admin/tips/delete/<?php echo $tip['id']; ?>">Delete</a>
						</td>
						<td>
							<input type="submit" name="approve_tip[<?php echo $tip['id']; ?>]" value="&check;">&nbsp;
							<input type="submit" name="reject_tip[<?php echo $tip['id']; ?>]" value="&times;">
						</td>
					</tr>
<?php endforeach; ?>
				</table>
				<h4>Rejected Tips</h4>
				<table cellspacing="0">
					<tr>
						<th>Created</th>
						<th>User</th>
						<th>Message</th>
						<th>Actions</th>
						<th>Status</th>
					</tr>
<?php foreach ($tips['REJECTED'] as $tip): ?>
					<tr>
						<td><?php echo $tip['time']; ?></td>
						<td><?php echo $tip['user']; ?></td>
						<td><?php echo $tip['message']; ?></td>
						<td>
							<a href="/admin/tips/edit/<?php echo $tip['id']; ?>">Edit</a>&nbsp;&bull;&nbsp;
							<a href="/admin/tips/delete/<?php echo $tip['id']; ?>">Delete</a>
						</td>
						<td>
							<input type="submit" name="approve_tip[<?php echo $tip['id']; ?>]" value="&check;">
						</td>
					</tr>
<?php endforeach; ?>
				</table>
			</form>
		</section>
<?php endif; ?>
	</section>
