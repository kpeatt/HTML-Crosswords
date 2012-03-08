<?php 

echo form_open($this->uri->uri_string());

if (isset($fbuser) && !empty($email)) {
$email = $fbuser['email'];

}

 ?>
	<table>
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" id="username" value="<?php echo set_value('username') != null ? set_value('username') : ''; ?>"/></td>
			<td style="color: red;"><?php echo form_error('username'); ?><?php echo isset($errors['username'])?$errors['username']:''; ?></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="text" name="email" id="email" value="<?php if (isset($fbuser) && !empty($email)) { echo $email; } else { echo set_value('email'); } ?>"/></td>
			<td style="color: red;"><?php echo form_error('email'); ?><?php echo isset($errors['email'])?$errors['email']:''; ?></td>
		</tr>
	</table>
	<input type="submit" name="submit" value="Let me in" />
<?php echo form_close(); ?>
				