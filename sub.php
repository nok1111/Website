<?php require_once 'engine/init.php'; require_once 'layout/overall/header.php';
if ($config['allowSubPages']) {
	$page = (isset($_GET['page']) && !empty($_GET['page'])) ? getValue($_GET['page']) : '';
	if (isset($subpages[$page]['file'])) require_once 'layout/sub/'.$subpages[$page]['file'];
	else {
		if (isset($subpages)) echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center"><h2>Sub page not recognized.</h2><p>The sub page you requested is not recognized.</p></div></div>';
	}
}
else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center"><h2>System disabled.</h2><p>The sub page system is disabled.</p></div></div>';
require_once 'layout/overall/footer.php'; ?>
