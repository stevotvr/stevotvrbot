	<section id="inventory">
		<header>
			<h1>Inventory</h1>
		</header>
<?php foreach ($inventory as $user => $items): ?>
		<section class="user">
			<header>
				<h2><?php echo $user; ?></h2>
			</header>
			<ul>
<?php foreach ($items as $item): ?>
				<li>
					<h3><?php echo $item['item']; ?></h3>
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
			</footer>
		</section>
<?php endforeach; ?>
	</section>
