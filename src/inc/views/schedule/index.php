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
					<th>Platform</th>
				</tr>
<?php foreach ($schedule as $item): ?>
				<tr>
					<td><?php echo $item['day']; ?></td>
					<td><?php echo $item['time']; ?></td>
<?php if (!$singleGame): ?>
					<td class="nowrap"><?php echo $item['game']; ?></td>
<?php endif; ?>
					<td><a href="<?php echo $item['platform']['url']; ?>"><?php echo $item['platform']['name']; ?></a></td>
				</tr>
<?php endforeach; ?>
			</table>
		</section>
	</section>
