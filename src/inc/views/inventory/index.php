<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Inventory</title>
</head>
<body>
	<h1>Inventory</h1>
<?php foreach ($inventory as $user => $items): ?>
	<h2><?php echo $user; ?></h2>
	<ul>
<?php foreach ($items as $item): ?>
		<li><?php echo $item['description']; ?> worth $<?php echo $item['value']; ?><?php if ($item['quantity'] > 1): ?> (x<?php echo $item['quantity']; ?>)<?php endif; ?></li>
<?php endforeach; ?>
	</ul>
<?php endforeach; ?>
</body>
</html>
