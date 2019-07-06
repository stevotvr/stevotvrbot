	<section class="main" id="commands">
		<header>
			<h2><a href="/commands">Chat Commands</a></h2>
		</header>
<?php foreach ($commands as $level): ?>
		<section>
			<header>
				<h3><?php echo $level['name']; ?></h3>
			</header>
			<table cellspacing="0">
				<tr>
					<th>Command</th>
					<th>Description</th>
				</tr>
<?php foreach ($level['commands'] as $command): ?>
				<tr>
					<td>!<?php echo $command['name']; ?></td>
					<td><?php echo $command['description']; ?></td>
				</tr>
<?php endforeach; ?>
			</table>
		</section>
<?php endforeach; ?>
	</section>
