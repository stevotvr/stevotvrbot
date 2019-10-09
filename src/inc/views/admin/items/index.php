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
		<section>
			<table cellspacing="0">
				<tr>
					<th>Item Name</th>
					<th>Value</th>
					<th>Quantity</th>
					<th>Action</th>
				</tr>
<?php foreach ($items as $item): ?>
				<tr>
					<td><?php echo $item['name']; ?></td>
					<td><?php echo $item['value']; ?></td>
					<td><?php echo $item['quantity']; ?></td>
					<td>
						<a href="/admin/items/edit/<?php echo $item['slug']; ?>">Edit</a>&nbsp;&bull;
						<a href="/admin/items/delete/<?php echo $item['slug']; ?>">Delete</a>
					</td>
				</tr>
<?php endforeach; ?>
			</table>
			<footer>
				<a href="/admin/items/add">Add Item</a>
			</footer>
		</section>
<?php endif; ?>
	</section>
