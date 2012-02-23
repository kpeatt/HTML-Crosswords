<!-- Javascript -->
<?php 	
	if(isset($js) && !empty($js)){
	    foreach($js as $item) {
	        $this->load->view('js/'.$item, $puzzle);
	    }
	}
?>

</body>
</html>