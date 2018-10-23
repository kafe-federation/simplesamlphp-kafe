<?php 
$base_dir = dirname(__FILE__);
$base_dir = preg_replace('|/modules/.+|','/www',$base_dir);
include_once $base_dir.'/user_layout_head.php'; 
?>
	<div class="login-kreonet-new">

		<?php if ($this->data['errorcode'] !== NULL) { ?>
		<p class="login-error">
			<span>
			<?php 
			if ($this->data['errorcode'] == 'WRONGUSERPASS') {
				echo 'Either no user with the given username could be found, or the password you gave was wrong. <br />
			Your IP will be blocked after 5 login failures ('.(5-@$_SESSION['try_times']).' times left). <br />Please check your username/password and try again. ';
			} else {
				echo htmlspecialchars($this->t('{errors:descr_' . $this->data['errorcode'] . '}', $this->data['errorparams'])); 
			}
			?>
			</span>
		</p>
		<?php } ?>

		<div class="login-kreonet-form">
			<form action="?" method="post" name="f">
<?php
foreach ($this->data['stateparams'] as $name => $value) {
	echo('<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />');
}
?>
				<fieldset>
					<legend class="hidden">LOGIN</legend>
					<ul>
						<li class="first">
							<label for="login-kreonet-id"><?php echo $this->t('{login:username}'); ?></label>
<?php
if ($this->data['forceUsername']) {
	echo '<strong style="font-size: medium">' . htmlspecialchars($this->data['username']) . '</strong>';
} else {
	echo '<input type="text" id="login-kreonet-id" name="username" value="' . htmlspecialchars($this->data['username']) . '" />';
}
?>
						</li>
						<li>
							<label for="login-kreonet-pw"><?php echo $this->t('{login:password}'); ?></label>
							<input type="password" autocomplete="off" name="password" id="login-kreonet-pw" />
						</li>
					</ul>
					<input type="submit" class="login-kreonet-btn" value="<?php echo $this->t('{login:login_button}'); ?>" />
					<span class="icon-warning">New user? or forgot your password?</span>
					<a href="<?php echo $url_ids; ?>" target="_blank" class="btn-gray"><span class="icon-newlink">Go to KREONET IMS</span></a>
				</fieldset>
			</form>
		</div>

		<div class="login-note">
			<strong>Please note</strong>
			<p>Before entering your username and password, <br />verify that the URL for this page begins with: https://coreen-idp.kreonet.net </p>
		</div>

<?php include_once $base_dir.'/user_layout_footer.php'; ?>
