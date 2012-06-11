<?php 
	
	if (isset($user) && $user != '') {
		$is_logged_in = true;
	}
	
?>

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

<body class="<?php echo $template['name']; ?>">

	<header class="navbar navbar-fixed-top">
	
		<div class="navbar-inner">
				<div class="container-fluid">
					<a class="brand" href="/">
						WordMist
					</a>
					
					<?php if (isset($is_logged_in) && $is_logged_in == true) { ?>
						<ul class="nav pull-right">
						  <li class="dropdown">
						    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
						    	<img src="<?php echo $user['avatar'] ?>" class="avatar">
						    	<?php echo $user['username'] ?>
						        <b class="caret"></b>
						    </a>
						    <ul class="dropdown-menu">
						    	<li>
						    		<a href="/user/profile"><i class="icon-user"></i> Account Settings</a>
						    	</li>
						    	<li class="divider"></li>
						    	<li>
						    		<a href="/auth/logout"><i class="icon-off"></i> Sign out</a>
						    	</li>
						    </ul>
						  </li>
						</ul>
						<?php } else { ?>
						<a class="btn btn-primary pull-right" href="/auth/login">
							Log In
						</a>
					<?php } ?>
				</div>
		</div>
	
	</header>
	
	<?php if ($this->session->flashdata('success') != '') { ?>
		<div class="alert alert-success">
			<?php echo $this->session->flashdata('success'); ?>
		</div>
	<?php } ?>
	
	<?php if ($this->session->flashdata('error') != '') { ?>
		<div class="alert alert-error">
			<?php echo $this->session->flashdata('error'); ?>
		</div>
	<?php } ?>