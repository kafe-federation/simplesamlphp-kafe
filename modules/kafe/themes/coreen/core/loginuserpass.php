<?php 
	$this->data['layout-header'] = true;
	$this->includeAtTemplateBase('includes/header-coreen.php');
?>
<!-- layout-container -->
<div id="layout-container">
	<div id="container">
		<!-- container-body -->
		<div id="container-body">
			<div id="contents">
				<div class="box-kreonet login-form">
					<form action="?" method="post" name="f">
					<?php
					foreach ($this->data['stateparams'] as $name => $value) {
						echo('<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />');
					}
					?>
					<p class="img">
						<img src="/<?php echo $this->data['baseurlpath']; ?>resources/coreen/images/kreonet_logo.gif" alt="organization" />
					</p>
					<ul>
						<li>
						<?php
						if ($this->data['forceUsername']) {
							echo '<strong>' . htmlspecialchars($this->data['username']) . '</strong>';
						} else {
							echo '<input type="text" placeholder="Username" name="username" value="' . htmlspecialchars($this->data['username']) . '" />';
						}
						?>				
						</li>
						<li><input type="password" autocomplete="off" name="password" placeholder="Password" /></li>
					</ul>
					<input type="submit" class="btn-purple" value="Login" />
					</form>
				</div>

				<div class="box-blue">
					<h2>Please note</h2>
					<p class="content">By accessing or using  this service, you represent that you have read, understood, and agree to be bound by this <a href="https://www.your.org/simplesaml/aup.php" target="_blank">User Agreement</a> including any future modifications.</p>
					<p class="content">Before entering your username and password, <br />verify that the URL for this page begins with: <a href="https://www.your.org" target="_blank">https://www.your.org</a></p>
				</div>

				<?php if ($this->data['errorcode'] !== NULL) { ?>
				<p class="icon-wrong">
					<?php 
					if ($this->data['errorcode'] == 'WRONGUSERPASS') {
						echo 'Either no username is found, or the password you gave was wrong. <br />
                                                      Note that your IP address is recorded for security purposes. <br />
                                                      Please check your username/password and try again. ';

					} else if($this->data['errorcode'] == 'NOPRIVILEGE') {
                                                echo 'No use right is granted. <br />
                                                      The service is available only for student, faculty, and staff enrolled in the organization. <br />
                                                      Please contact your IT administration department. ';
                                        } else {
						echo htmlspecialchars($this->t('{errors:descr_' . $this->data['errorcode'] . '}', $this->data['errorparams'])); 
					}
					?>
				</p>
				<?php } ?>

				<p class="newuser">
					Trouble in login? or forgot your password? <span>Contact your <a href="mailto:MAIN_ADMIN">IT Administration staffs</a></span>
				</p>
			</div>
		</div>
		<!-- //container-body -->
	</div>
</div>
<!-- //layout-container -->

<?php 
	$this->includeAtTemplateBase('includes/footer-coreen.php');
?>
