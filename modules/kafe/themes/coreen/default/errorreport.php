<?php
	$this->data['header'] = $this->t('errorreport_header');
	$this->includeAtTemplateBase('includes/header-coreen.php');
?>

<!-- layout-container -->
<div id="layout-container" class="mid">
	<div id="container">
		<!-- container-body -->
		<div id="container-body">
			<div id="contents">
				<div class="icon-ment">
					<span class="icon">
						<img src="/<?php echo $this->data['baseurlpath'].'resources/coreen/images/icon_mail.png'; ?>" alt="icon" />
					</span>
					<strong><?php echo $this->t('errorreport_header'); ?></strong>
					<p><?php echo $this->t('errorreport_text'); ?></p>
				</div>
			</div>
		</div>
		<!-- //container-body -->
	</div>
</div>
<!-- //layout-container -->

<?php
	$this->includeAtTemplateBase('includes/footer-coreen.php');
