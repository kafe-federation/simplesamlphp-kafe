<?php



/**
 * Support the htmlinject hook, which allows modules to change header, pre and post body on all pages.
 */
$this->data['htmlinject'] = array(
	'htmlContentPre' => array(),
	'htmlContentPost' => array(),
	'htmlContentHead' => array(),
);


$jquery = array();
if (array_key_exists('jquery', $this->data)) $jquery = $this->data['jquery'];

if (array_key_exists('pageid', $this->data)) {
	$hookinfo = array(
		'pre' => &$this->data['htmlinject']['htmlContentPre'], 
		'post' => &$this->data['htmlinject']['htmlContentPost'], 
		'head' => &$this->data['htmlinject']['htmlContentHead'], 
		'jquery' => &$jquery, 
		'page' => $this->data['pageid']
	);
		
	SimpleSAML_Module::callHooks('htmlinject', $hookinfo);	
}
// - o - o - o - o - o - o - o - o - o - o - o - o -

/**
 * Do not allow to frame SimpleSAMLphp pages from another location.
 * This prevents clickjacking attacks in modern browsers.
 *
 * If you don't want any framing at all you can even change this to
 * 'DENY', or comment it out if you actually want to allow foreign
 * sites to put SimpleSAMLphp in a frame. The latter is however
 * probably not a good security practice.
 */
header('X-Frame-Options: SAMEORIGIN');

// SP 이름 얻어
$spName = array();
if (isset($this->data['SPMetadata'])) {
	if (array_key_exists('name', $this->data['SPMetadata'])) {
		$spName = $this->data['SPMetadata']['name'];
	} elseif (array_key_exists('OrganizationDisplayName', $this->data['SPMetadata'])) {
		$spName = $this->data['SPMetadata']['OrganizationDisplayName'];
	} else {
		$spName = $this->data['SPMetadata']['entityid'];
	}
	if (is_array($spName)) {
		$spName = $this->t($spName);
	}
	$spName = htmlspecialchars($spName);
}

if (!isset($this->data['header'])) {
	$this->data['header'] = 'LOGIN to '.$spName;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<script type="text/javascript" src="/<?php echo $this->data['baseurlpath']; ?>resources/script.js"></script>
<title><?php echo $this->data['header']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="/<?php echo $this->data['baseurlpath']; ?>resources/coreen/css/default.css" />
<?php
if(!empty($jquery)) {
	// SSP 의 버전을 체크해서 Jquery 로드 결정
	$version = '1.6';
	$config = SimpleSAML_Configuration::getInstance();	
	if (strpos($config->getVersion(), '1.14') === 0) {
		$version = '1.8';
	}

	if (array_key_exists('version', $jquery)) {
		$version = $jquery['version'];
	}

	if ($version == '1.6') {
		if (isset($jquery['core']) && $jquery['core'])
			echo('<script type="text/javascript" src="/' . $this->data['baseurlpath'] . 'resources/jquery-16.js"></script>' . "\n");
	
		if (isset($jquery['ui']) && $jquery['ui'])
			echo('<script type="text/javascript" src="/' . $this->data['baseurlpath'] . 'resources/jquery-ui-16.js"></script>' . "\n");
	
		if (isset($jquery['css']) && $jquery['css'])
			echo('<link rel="stylesheet" media="screen" type="text/css" href="/' . $this->data['baseurlpath'] . 
				'resources/uitheme16/ui.all.css" />' . "\n");	
	} else if ($version == '1.8') {
		if (isset($jquery['core']) && $jquery['core'])
			echo('<script type="text/javascript" src="/' . $this->data['baseurlpath'] . 'resources/jquery-1.8.js"></script>' . "\n");
	
		if (isset($jquery['ui']) && $jquery['ui'])
			echo('<script type="text/javascript" src="/' . $this->data['baseurlpath'] . 'resources/jquery-ui-1.8.js"></script>' . "\n");
	
		if (isset($jquery['css']) && $jquery['css'])
			echo('<link rel="stylesheet" media="screen" type="text/css" href="/' . $this->data['baseurlpath'] . 
				'resources/uitheme1.8/jquery-ui.css" />' . "\n");
	}
}

if (isset($this->data['clipboard.js'])) {
	echo '<script type="text/javascript" src="/'. $this->data['baseurlpath'] .
		 'resources/clipboard.min.js"></script>'."\n";
}

if(!empty($this->data['htmlinject']['htmlContentHead'])) {
	foreach($this->data['htmlinject']['htmlContentHead'] AS $c) {
		echo $c;
	}
}
	
if(array_key_exists('head', $this->data)) {
	echo '<!-- head -->' . $this->data['head'] . '<!-- /head -->';
}
?>
</head>
<?php
$onLoad = '';
if (array_key_exists('autofocus', $this->data)) {
	$onLoad .= 'SimpleSAML_focus(\'' . $this->data['autofocus'] . '\');';
}
if (isset($this->data['onLoad'])) {
	$onLoad .= $this->data['onLoad']; 
}

if ($onLoad !== '') {
	$onLoad = ' onload="' . $onLoad . '"';
}
?>
<body<?php echo $onLoad; ?>>

<?php
if (!empty($this->data['htmlinject']['htmlContentPre'])) {
	foreach($this->data['htmlinject']['htmlContentPre'] AS $c) {
		echo $c;
	}
}
?>

<?php if (isset($this->data['layout-header'])) { ?>
<!-- layout-header -->
<div id="layout-header">
	<div id="header">
		<h1 id="logo"><a href="/">LOGIN <span>to <?php echo $spName; ?></span></a></h1>
	</div>
</div>
<!-- //layout-header -->
<?php } ?>
