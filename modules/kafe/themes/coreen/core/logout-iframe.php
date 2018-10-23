<?php

$id = $this->data['id'];
$type = $this->data['type'];
$from = $this->data['from'];
$SPs = $this->data['SPs'];

$stateImage = array(
	'unsupported' => '/' . $this->data['baseurlpath'] . 'resources/icons/silk/delete.png',
	'completed' => '/' . $this->data['baseurlpath'] . 'resources/icons/silk/accept.png',
	'onhold' => '/' . $this->data['baseurlpath'] . 'resources/icons/bullet16_grey.png',
	'inprogress' => '/' . $this->data['baseurlpath'] . 'resources/progress.gif',
	'failed' => '/' . $this->data['baseurlpath'] . 'resources/icons/silk/exclamation.png',
);

$stateText = array(
	'unsupported' => '',
	'completed' => $this->t('{logout:completed}'),
	'onhold' => '',
	'inprogress' => $this->t('{logout:progress}'),
	'failed' => $this->t('{logout:failed}'),
);

$spStatus = array();
$spTimeout = array();
$nFailed = 0;
$nProgress = 0;
foreach ($SPs as $assocId => $sp) {
	assert('isset($sp["core:Logout-IFrame:State"])');
	$state = $sp['core:Logout-IFrame:State'];
	$spStatus[sha1($assocId)] = $state;
	if (isset($sp['core:Logout-IFrame:Timeout'])) {
		$spTimeout[sha1($assocId)] = $sp['core:Logout-IFrame:Timeout'] - time();
	} else {
		$spTimeout[sha1($assocId)] = 5;
	}
	if ($state === 'failed') {
		$nFailed += 1;
	} elseif ($state === 'inprogress') {
		$nProgress += 1;
	}
}

if ($from !== NULL) {
	$from = $this->getTranslation($from);
}

if (!isset($this->data['head'])) {
	$this->data['head'] = '';
}

$this->data['head'] .= '
<script type="text/javascript" language="JavaScript">
window.stateImage = ' . json_encode($stateImage) . ';
window.stateText = ' . json_encode($stateText) . ';
window.spStatus = ' . json_encode($spStatus) . ';
window.spTimeout = ' . json_encode($spTimeout) . ';
window.type = "' . $type . '";
window.asyncURL = "logout-iframe.php?id=' . $id . '&type=async";
</script>';

$this->data['head'] .= '<script type="text/javascript" src="logout-iframe.js"></script>';
$this->data['header'] = $this->t('{logout:progress}');
$this->includeAtTemplateBase('includes/header-coreen.php');
?>

<!-- layout-container -->
<div id="layout-container">
	<div id="container">
		<!-- container-body -->
		<div id="container-body">
			<div id="contents">

				<div class="box-kreonet">
					<p class="img">
						<img src="/<?php echo $this->data['baseurlpath']; ?>resources/coreen/images/kreonet_logo.gif" alt="logo" />
					</p>

					<?php if ($from !== NULL) { ?>
					<p class="icon-ment">
						<span class="icon">
							<img src="/<?php echo $this->data['baseurlpath']; ?>resources/coreen/images/icon_logout.png" alt="icon" />
						</span>
						<strong>
							<?php echo $this->t('{logout:loggedoutfrom}', array('%SP%' =>  htmlspecialchars($from))); ?>
						</strong>
					</p>
					<?php } ?>
					
					<div class="box-blue">
						<p>
					<?php
							if ($type === 'init') {
								echo($this->t('{logout:also_from}'));
							} else {
								echo($this->t('{logout:logging_out_from}'));
							}
					?>
						</p>
						<ul class="bul-dot2">
					<?php
						foreach ($SPs AS $assocId => $sp) {
							if (isset($sp['core:Logout-IFrame:Name'])) {
								$spName = $this->getTranslation($sp['core:Logout-IFrame:Name']);
							} else {
								$spName = $assocId;
							}

							assert('isset($sp["core:Logout-IFrame:State"])');
							$spState = $sp['core:Logout-IFrame:State'];
							$spId = sha1($assocId);

							echo '<li>';
							echo '<img class="logoutstatusimage" id="statusimage-' . $spId . '"  src="' . htmlspecialchars($stateImage[$spState]) . '" alt="' . htmlspecialchars($stateText[$spState]) . '"/> ';
							echo '<strong>'.htmlspecialchars($spName).'</strong>';
							echo '</li>';
						}
					?>
						</ul>
					</div>
				</div>

				<div class="btn-area">
				<?php if ($type === 'init') { ?>
					<strong><?php echo $this->t('{logout:logout_all_question}'); ?></strong>

					<form id="startform" method="get" action="logout-iframe.php">
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" id="logout-type-selector" name="type" value="nojs" />
						<input type="submit" class="btn-purple" id="logout-all" name="ok" value="<?php echo $this->t('{logout:logout_all}'); ?>" />
					</form>

					<?php
						if (isset($from)) {
							$logoutCancelText = $this->t('{logout:logout_only}', array('%SP%' => htmlspecialchars($from)));
						} else {
							$logoutCancelText = $this->t('{logout:no}');
						}
					?>
					<form method="get" action="logout-iframe-done.php">
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="submit" class="btn-gray" name="cancel" value="<?php echo $logoutCancelText; ?>" />
					</form>
				<?php } else { ?>
					<div id="logout-failed-message" style="<?php echo ($nFailed > 0) ? '' : 'display: none;'?>">
					<strong><?php echo $this->t('{logout:failedsps}'); ?></strong>
					<form method="post" action="logout-iframe-done.php" id="failed-form" target="_top">
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<button type="submit" name="continue" class="btn-gray"><?php echo $this->t('{logout:return}'); ?></button>
					</form>
					</div>

					<div id="logout-completed" style="<?php echo ($nProgress == 0 && $nFailed == 0) ? '' : 'display:none;'; ?>">
					<strong><?php echo $this->t('{logout:success}'); ?></strong>
					<form method="post" action="logout-iframe-done.php" id="done-form" target="_top">
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<button type="submit" name="continue" class="btn-gray"><?php echo $this->t('{logout:return}'); ?></button>
					</form>
					</div>
					<?php
						if ($type === 'js') {
							foreach ($SPs AS $assocId => $sp) {
								$spId = sha1($assocId);

								if ($sp['core:Logout-IFrame:State'] !== 'inprogress') {
									continue;
								}
								assert('isset($sp["core:Logout-IFrame:URL"])');

								$url = $sp["core:Logout-IFrame:URL"];

								echo('<iframe style="width:0; height:0; border:0;" src="' . htmlspecialchars($url) . '"></iframe>');
							}
						}
					?>
				<?php } ?>
				</div>
			</div>
		</div>
		<!-- //container-body -->
	</div>
</div>
<!-- //layout-container -->

<?php
	$this->includeAtTemplateBase('includes/footer-coreen.php');

