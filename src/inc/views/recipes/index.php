	<section class="main" id="recipes">
		<header>
			<h2><a href="/recipes">Item Crafting</a></h2>
		</header>
<?php if (!empty($recipes)): ?>
		<section>
			<header>
				<h3>Recipes</h3>
			</header>
			<ul>
<?php foreach ($recipes as $item => $recipe): ?>
				<li>
					<header>
						<h4><?php echo $item; ?></h4>
					</header>
					<ul>
<?php foreach ($recipe as $ingredient): ?>
						<li><?php echo $ingredient['quantity']; ?>x <?php echo $ingredient['ingredient']; ?></li>
<?php endforeach; ?>
					</ul>
				</li>
<?php endforeach; ?>
			</ul>
			<footer>!craft &lt;item&gt;</footer>
		</section>
<?php else: ?>
		<section>
			<p>No recipes found.</p>
		</section>
<?php endif; ?>
	</section>
