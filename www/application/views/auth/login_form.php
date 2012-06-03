<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'value' => set_value('login'),
	'maxlength'	=> 80,
);
if ($login_by_username AND $login_by_email) {
	$login_label = 'Email or login';
} else if ($login_by_username) {
	$login_label = 'Login';
} else {
	$login_label = 'Email';
}
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
);
$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember'),
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);
?>
		<div class="page-header">	
			<h1>Sign in to your WordMist Account</h1>
		</div>
		
		<div class="row">
		
			<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
			
				<div class="control-group">
					<?php echo form_label($login_label, $login['id']); ?>
					<div class="controls">
						<?php echo form_input($login); ?>
						<?php echo form_error($login['name']); ?><?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?>
					</div>
				</div>
				<div class="control-group">
					<?php echo form_label('Password', $password['id']); ?>
					<div class="controls">
						<?php echo form_password($password); ?>
						<?php echo form_error($password['name']); ?><?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?>
						<p class="help-inline"><?php echo anchor('/auth/forgot_password/', 'Forgot password'); ?></p>
					</div>
			
				<?php if ($show_captcha) {
					if ($use_recaptcha) { ?>
					
						<div id="recaptcha_image"></div>
						<a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
						<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
						<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
	
						<div class="recaptcha_only_if_image">Enter the words above</div>
						<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
	
						<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
						<?php echo form_error('recaptcha_response_field'); ?>
						<?php echo $recaptcha_html; ?>
	
				<?php } else { ?>
	
						<p>Enter the code exactly as it appears:</p>
						<?php echo $captcha_html; ?>
	
					<?php echo form_label('Confirmation Code', $captcha['id']); ?>
					<?php echo form_input($captcha); ?>
					<?php echo form_error($captcha['name']); ?>
	
				<?php }
				} ?>
				
					<div class="control-group">
						<div class="controls">
							<label for="<?php echo $remember['id']; ?>" class="checkbox">
								<?php echo form_checkbox($remember); ?>
								Remember me
							</label>
						</div>
					</div>
					
					<div class="control-group">
						<div class="controls">
							<?php echo form_submit('submit', 'Let me in', 'class="btn btn-primary btn-large"'); ?>
							<?php if ($this->config->item('allow_registration', 'tank_auth')) { ?><p class="help-block"><?php echo anchor('/auth/register/', 'Register for an Account'); ?></p><?php } ?>
						</div>
					</div>
					
					<div class="control-group">
						<div class="controls">
							<p class="help-block"><fb:login-button v="2" perms="" length="long" onlogin='window.location="https://graph.facebook.com/oauth/authorize?client_id=<?php echo $this->config->item('facebook_app_id'); ?>&redirect_uri=<?php echo site_url('auth_other/fb_signin'); ?>&scope=email&amp;r="+window.location.href;'></fb:login-button></p>
							
							<p class="help-block"><a class="twitter" href="<?php echo site_url('auth_other/twitter_signin'); ?>">
								<img style="margin-top:5px;" src="<?php echo base_url(); ?>img/twitter_login_button.gif" alt="twitter login" border="0"/>
							</a></p>
							
							<p class="help-block"><a href="<?php echo site_url('auth_other/google_openid_signin'); ?>">
								<img style="margin-top:5px;" src="<?php echo base_url(); ?>img/google_connect_button.png" alt="google open id" border="0"/>
							</a></p>
					
							<p class="help-block"><a href="<?php echo site_url('auth_other/yahoo_openid_signin'); ?>">
								<img style="margin-top:5px;" src="<?php echo base_url(); ?>img/yahoo_openid_connect.png" alt="yahoo open id" border="0"/>
							</a></p>
							
							<p id="viewer-info"></p>
							
							<div id="fb-root"></div>
							<script src="http://connect.facebook.net/en_US/all.js"></script>
							<script type="text/javascript">
							  	FB.init({appId: "<?php echo $this->config->item('facebook_app_id'); ?>", status: true, cookie: true, xfbml: true});
							  	FB.Event.subscribe('auth.sessionChange', function(response) {
							    	if (response.session) 
							    	{
							      		// A user has logged in, and a new cookie has been saved
										//window.location.reload(true);
							    	} 
							    	else 
							    	{
							      		// The user has logged out, and the cookie has been cleared
							    	}
							  	});
							</script>
							
						</div>
					</div>
					
			<?php echo form_close(); ?>
		
		</div>