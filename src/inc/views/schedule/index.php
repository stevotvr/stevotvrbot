	<section class="main" id="schedule">
		<header>
			<h2><a href="/schedule">Stream Schedule</a></h2>
		</header>
<?php if ($singleGame): ?>
		<section>
			<p>Currently Playing <strong><?php echo $game; ?></strong></p>
		</section>
<?php endif; ?>
		<section>
			<table cellspacing="0">
				<tr>
					<th>Day</th>
					<th>Time</th>
<?php if (!$singleGame): ?>
					<th>Game</th>
<?php endif; ?>
				</tr>
<?php foreach ($schedule as $item): ?>
				<tr>
					<td><?php echo $item['day']; ?></td>
					<td><?php echo $item['time']; ?></td>
<?php if (!$singleGame): ?>
					<td><?php echo $item['game']; ?></td>
<?php endif; ?>
				</tr>
<?php endforeach; ?>
			</table>
		</section>
	</section>
