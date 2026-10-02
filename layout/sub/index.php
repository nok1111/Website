<?php
if ($config['UseChangelogTicker']) {
	//////////////////////
	// Changelog ticker //
	// Load from cache
	$changelogCache = new Cache('engine/cache/changelog');
	$changelogCache->useMemory(false);
	$changelogs = $changelogCache->load();

	if (isset($changelogs) && !empty($changelogs) && $changelogs !== false) {
		?><center><br><div class="main-content__title-text"><span>LATEST</span> CHANGELOGS</div></center><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px">
		<div class="well">
			<table id="changelogTable">
				<?php
				for ($i = 0; $i < count($changelogs) && $i < 5; $i++) {
					?>
					<tr>
						<td><div class="desc-changelog__block flex-ss"><?php echo getClock($changelogs[$i]['time'], true, true); ?></div></td>
						<td><div class="desc-changelog__block flex-ss"><?php echo $changelogs[$i]['text']; ?></div></td>
					</tr>
					<?php
				}
				?>
			</table>
		</div></div>
		<?php
	} else echo "No changelogs submitted.";
}

$cache = new Cache('engine/cache/news');
if ($cache->hasExpired()) {
	$news = fetchAllNews();
	$cache->setContent($news);
	$cache->save();
} else {
	$news = $cache->load();
}

// Design and present the list
if ($news) {

	$total_news = count($news);
	$row_news = $total_news / $config['news_per_page'];
	$page_amount = ceil($total_news / $config['news_per_page']);
	$current = $config['news_per_page'] * $page;

	function TransformToBBCode($string) {
		$tags = array(
			'[center]{$1}[/center]' => '<center>$1</center>',
			'[b]{$1}[/b]' => '<b>$1</b>',
			'[size={$1}]{$2}[/size]' => '<font size="$1">$2</font>',
			'[img]{$1}[/img]'    => '<a href="$1" target="_BLANK"><img src="$1" alt="image" style="width: 100%"></a>',
			'[link]{$1}[/link]'    => '<a href="$1">$1</a>',
			'[link={$1}]{$2}[/link]'   => '<a href="$1" target="_BLANK">$2</a>',
			'[color={$1}]{$2}[/color]' => '<font color="$1">$2</font>',
			'[*]{$1}[/*]' => '<li>$1</li>',
			'[youtube]{$1}[/youtube]' => '<div class="youtube"><div class="aspectratio"><iframe src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div></div>',
		);
		foreach ($tags as $tag => $value) {
			$code = preg_replace('/placeholder([0-9]+)/', '(.*?)', preg_quote(preg_replace('/\{\$([0-9]+)\}/', 'placeholder$1', $tag), '/'));
			$string = preg_replace('/'.$code.'/i', $value, $string);
		}
		return $string;
	}

	if ($view !== "") { // We want to view a specific news post
		$si = false;
		if (ctype_digit($view) === false) {
			for ($i = 0; $i < count($news); $i++) if ($view === urlencode($news[$i]['title'])) $si = $i;
		} else {
			for ($i = 0; $i < count($news); $i++) if ((int)$view === (int)$news[$i]['id']) $si = $i;
		}

		if ($si !== false) {
			echo "hello world!";
			?>
			
			<center><br><div class="main-content__title-text"><span>LATEST</span> NEWS</div></center><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px">
			<div class="desc-changelog__block flex-ss">
			<div class="postHolder">
				<div class="well">
					<div class="header_news">
						<?php echo '<a href="?view='.$news[$si]['id'].'">[#'.$news[$si]['id'].']</a> '. getClock($news[$si]['date'], true) .' by <a href="characterprofile.php?name='. $news[$si]['name'] .'">'. $news[$si]['name'] .'</a> - <b>'. TransformToBBCode($news[$si]['title']) .'</b>'; ?>
					</div>
					<div class="body">
					<br></br>
						<p><?php echo TransformToBBCode(nl2br($news[$si]['text'])); ?></p>
					</div>
				</div>
			</div></div></div>
			<!-- OLD DESIGN: -->
			<?php
		} else {
			?><center><br><div class="main-content__title-text"><span>LATEST</span> NEWS</div></center><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px">
			<div class="desc-changelog__block flex-ss">
			<table id="news">
				<tr class="yellow">
					<td class="zheadline">News post not found.</td>
				</tr>
				<tr>
					<td>
						<p>We failed to find the post you where looking for.</p>
					</td>
				</tr>
			</table>
		</div></div>
			<?php
		}

	} else { // We want to view latest news or a page of news.
		for ($i = $current; $i < $current + $config['news_per_page']; $i++) {
			if (isset($news[$i])) {
				?><center><br><div class="main-content__title-text"><span><?php echo '<a href="?view='.urlencode($news[$i]['title']).'">'.getClock($news[$i]['date'], true).''; ?></span> Posted by <?php echo '<a href="characterprofile.php?name='. $news[$i]['name'] .'">'. $news[$i]['name'] .'</a>'; ?></div></center><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px">
				<div class="desc-changelog__block flex-ss">
				<div class="postHolder">
					<div class="well">
						<div class="header_news">
						</div>
						<div class="body">
							<p><?php echo TransformToBBCode(nl2br($news[$i]['text'])); ?></p>
						</div>
					</div>
				</div></div></div>
				<?php
			}
		}

		echo '<center><select class="btn yellow flex-cc header__info-button" name="newspage" onchange="location = this.options[this.selectedIndex].value;">';

		for ($i = 0; $i < $page_amount; $i++) {

			if ($i == $page) {

				echo '<option class="btn yellow flex-cc header__info-button" value="index.php?page='.$i.'" selected>Page '.$i.'</option>';

			} else {

				echo '<option class="btn yellow flex-cc header__info-button" value="index.php?page='.$i.'">Page '.$i.'</option>';
			}
		}

		echo '</select></center>';

	}

} else {
	echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss"><p>No news exist.</p></div></div>';
}
?>
