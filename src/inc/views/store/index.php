	<section class="main" id="store">
		<header>
			<h2><a href="/store">Item Store</a></h2>
		</header>
<?php if (!empty($store)): ?>
		<section>
			<ul>
<?php foreach ($store as $item): ?>
				<li>
					<h4><?php echo $item['item']; ?></h4>
					</header>
					<dl>
						<dt>Value:</dt>
						<dd><?php echo $item['value']; ?> <?php echo $pointsName; ?></dd><br>
						<dt>Quantity:</dt>
						<dd><?php echo $item['quantity']; ?></dd>
					</dl>
				</li>
<?php endforeach; ?>
			</ul>
			<footer>!store &lt;buy|sell&gt; &lt;item&gt;</footer>
		</section>
<?php else: ?>
		<section>
			<p>No items found.</p>
		</section>
<?php endif; ?>
	</section>
