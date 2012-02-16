<?php 

$width = 15;
$height = 15;

$fill = "-----.----.---------.----.----------------------.-----.-----...---...---...--------------------.-----.-------.-----.-------.-----.--------------------...---...---...-----.-----.----------------------.----.---------.----.-----";

$cells = str_split($fill);

?>

<style type="text/css">

	table {
		border: 1px solid black;
		border-collapse: collapse;
		empty-cells: show;
	}
	
	td {
		display: table-cell;
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