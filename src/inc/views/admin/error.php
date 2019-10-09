<?php if (isset($error) && !empty($error)): ?>
		<section class="error">
			<ul>
<?php foreach ((array) $error as $e): ?>
				<li></php echo $e; ?></li>
<?php endforeach; ?>
			</ul>
		</section>
<?php endif; ?>
