<?php foreach($puzzles as $puzzle): ?>

	<h2><?php echo $puzzle['meta']['title'] ?></h2>
	<p><?php echo $puzzle['meta']['author'].", ".$puzzle['meta']['copyright'] ?></p>
	
	<p><a href="puzzles/<?php echo $puzzle['slug'] ?>">View Puzzle</a></p>
	
<?php endforeach ?>