	<section class="main" id="schedule">
		<header>
			<h2><a href="/schedule">Stream Schedule</a></h2>
		</header>
		<section>
			<table cellspacing="0">
				<tr>
					<th>Day</th>
					<th>Time</th>
					<th>Game</th>
				</tr>
<?php foreach ($schedule as $item): ?>
				<tr>
					<td><?php echo $item['day']; ?></td>
					<td><?php echo $item['time']; ?></td>
					<td><?php echo $item['game']; ?></td>
				</tr>
<?php endforeach; ?>
			</table>
		</section>
	</section>
