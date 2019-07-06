	<section class="main" id="inventory">
		<header>
			<h2><a href="/inventory">Inventory</a> - <?php echo $user; ?></h2>
		</header>
<?php if (!empty($inventory)): ?>
<?php foreach ($inventory as $name => $user): ?>
		<section>
			<header>
				<h3><?php echo $name; ?></h3>
			</header>
			<ul>
<?php foreach ($user['items'] as $item): ?>
				<li>
					<h4><?php echo $item['item']; ?></h4>
					</header>
					<dl>
						<dt>Status:</dt>
						<dd><?php echo $item['modifier']; ?></dd><br>
						<dt>Value:</dt>
						<dd>$<?php echo $item['value']; ?></dd><br>
						<dt>Quantity:</dt>
						<dd><?php echo $item['quantity']; ?></dd>
					</dl>
				</li>
<?php endforeach; ?>
			</ul>
			<footer>
				<dl>
					<dt>Total:</dt>
					<dd><?php echo $user['total']['items']; ?> items worth $<?php echo $user['total']['value']; ?></dd>
				</dl>
			</footer>
		</section>
<?php endforeach; ?>
<?php else: ?>
		<section>
			<p>No items found.</p>
		</section>
<?php endif; ?>
	</section>
