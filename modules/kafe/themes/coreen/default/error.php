<?php
$this->data['header'] = $this->t($this->data['dictTitle']);

$this->data['head'] = <<<EOF
<meta name="robots" content="noindex, nofollow" />
<meta name="googlebot" content="noarchive, nofollow" />
EOF;

$this->includeAtTemplateBase('includes/header-coreen.php');
?>

<!-- layout-container -->
<div id="layout-container">
	<div id="container">
		<!-- container-body -->
		<div id="container-body">
			<div id="contents">
				<h1 class="title-txt"><?php echo $this->t($this->data['dictTitle']); ?></h1>
				<p><?php echo htmlspecialchars($this->t($this->data['dictDescr'], $this->data['parameters'])); ?></p>

				<div class="box-blue">
					<?php
					// include optional information for error
					if (isset($this->data['includeTemplate'])) {
						$this->includeAtTemplateBase($this->data['includeTemplate']);
					}
					?>

					<p><?php echo $this->t('report_trackid').' '.$this->data['error']['trackId']; ?></p>
				</div>

			<?php if ($this->data['showerrors']) { ?>
				<h1 class="title-txt"><?php echo $this->t('debuginfo_header'); ?></h1>
				<p><?php echo $this->t('debuginfo_text'); ?></p>

				<div class="box-blue">
					<p><strong><?php echo htmlspecialchars($this->data['error']['exceptionMsg']); ?></strong></p>
					<p>
						<?php echo nl2br(htmlspecialchars($this->data['error']['exceptionTrace'])); ?>
					</p>
				</div>
			<?php } ?>

			<?php if (isset($this->data['errorReportAddress'])) { ?>
				<h1 class="title-txt"><?php echo $this->t('report_header'); ?></h1>
				<p><?php echo $this->t('report_text'); ?></p>
				<form action="<?php echo htmlspecialchars($this->data['errorReportAddress']); ?>" method="post">
					<p class="email-form">
						<label for="email">E-mail address:</label>
						<input type="text" size="25" id="email" name="email" value="<?php echo htmlspecialchars($this->data['email']); ?>" />
					</p>
					<p>
						<textarea class="metadatabox" name="text" style="width:100%" rows="6"><?php echo $this->t('report_explain'); ?></textarea>
					</p>
					<p>
						<input type="hidden" name="reportId" value="<?php echo $this->data['error']['reportId']; ?>"/>
						<button type="submit" name="send" class="btn-purple"><?php echo $this->t('report_submit'); ?></button>
					</p>
				</form>
			<?php } ?>

				<h1 class="title-txt"><?php echo $this->t('howto_header'); ?></h1>
				<p><?php echo $this->t('howto_text'); ?></p>

			</div>
		</div>
		<!-- //container-body -->
	</div>
</div>
<!-- //layout-container -->

<?php
$this->includeAtTemplateBase('includes/footer-coreen.php');
