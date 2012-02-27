<!DOCTYPE html>
<html lang="eng">

<head>
	<meta charset="utf-8">
	
	<title><?php echo $title; ?></title>

	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="robots" content="" />
	
	<?php 	
	if(isset($css) && !empty($css)){
	    foreach($css as $item) {
	        $this->load->view('css/'.$item);
	    }
	}
	?>

</head>

<body>