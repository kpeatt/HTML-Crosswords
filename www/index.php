<?php 

$fh = fopen("/Users/kyle/HTML-Crosswords/www/puzzles/cs120216.puz", "rb");

$data = fread($fh, filesize("/Users/kyle/HTML-Crosswords/www/puzzles/cs120216.puz"));
fclose($fh);

$dimdata = unpack("c2dim", substr($data, 0x2C, 2));

$width = $dimdata['dim1'];
$height = $dimdata['dim2'];

$answerstring = substr($data, 0x34, $width*$height); //Find the answers string

for ($i = 0; $i <= $height-1; ++$i) { // Make a 2d array of answers
	$answergrid[$i] = preg_split('//', substr($answerstring, $i*$width, $width));
}

$bwstring = substr($data, 0x34+$width*$height, $width*$height); //Find the crossword structure

for ($i = 0; $i < $height; ++$i) { // Make a 2d array of structure
	$bwgrid[$i] = str_split(substr($bwstring, $i*$width, $width));
}

$cluestring = substr($data, 0x34+($width*$height+$width*$height));

$newclues = preg_split('/\0/', $cluestring);

$header = array("title" => array_shift($newclues), "author" => array_shift($newclues), "copyright" => array_shift($newclues));

for ($i = 0; $i < count($bwgrid); ++$i) { // 2d Array of Clue Numbers
	for ($j = 0; $j < count($bwgrid[$i]); ++$j) {
		$numgrid[$i][$j] = $bwgrid[$i][$j];
	}
}

array_unshift($bwgrid, array()); // Add row of -1s to top and left of BWGrid
for ($i = 0; $i < $width; $i++) {
	array_push($bwgrid[0], -1);
}
for ($i = 0; $i <= $width; $i++) {
	array_unshift($bwgrid[$i], -1);
}

array_unshift($numgrid, array()); // Do the same thing to NumGrid
for ($i = 0; $i < $width; $i++) {
	array_push($numgrid[0], -1);
}
for ($i = 0; $i <= $width; $i++) {
	array_unshift($numgrid[$i], -1);
}



echo "<pre>";
print_r($numgrid);
echo "</pre>";

$cells = str_split($bwstring);

?>

<style type="text/css">

	table {
		border: 1px solid black;
		border-collapse: collapse;
		empty-cells: show;
	}
	
	td {
		width: 25px;
		height: 25px;
		border: 1px solid black;
	}
	
	td.black {
		background: black;
	}

</style>

<table>

	<?php
		
		$i = 1;
		
		foreach($cells as $cell) {
			
			if($i == 1) {
				echo "<tr>";
			}
			
			if($cell == '-') {
				$celltype = 'space';
			} elseif($cell == '.') {
				$celltype = 'black';
			}
			
			echo "<td class='".$celltype."'></td>";
			
			if($i == $width) {
				echo "</tr>";
			}
			
			$i++;
			
			if($i > $width) {
				$i = 1;
			}
		
		}
	
	?>

</table>