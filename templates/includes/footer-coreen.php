<?php
if(!empty($this->data['htmlinject']['htmlContentPost'])) {
	foreach($this->data['htmlinject']['htmlContentPost'] AS $c) {
		echo $c;
	}
}
?>
<!-- layout-footer -->
<div id="layout-footer">
	<div id="footer">
		<p class="kafe">
			<img src="/<?php echo $this->data['baseurlpath']; ?>resources/coreen/images/kafe.png" alt="KAFE" />
			Korean Access FEderation
		</p>

	        <ul id="fnb">
                <li></li>
                <li><a href="https://www.your.org/simplesaml/aup.php" target="_blank">TERMS OF USE</a></li>
                </ul>

	</div>
</div>
<!-- //layout-footer -->
</body>
</html>
