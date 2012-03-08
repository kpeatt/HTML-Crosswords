<!-- Javascript -->
<?php 	
	if(isset($js) && !empty($js)){
		
		if(isset($puzzle) && !empty($puzzle)) {$puzzle = $puzzle;} else {$puzzle = "";}
	
	    foreach($js as $item) {
	        $this->load->view('js/'.$item, $puzzle);
	    }
	}
?>

</body>
</html>