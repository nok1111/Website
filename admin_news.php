<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
protect_page();
admin_only($user_data);


// Recieving POST
if (empty($_POST) === false) {
	list($action, $id) = explode('!', sanitize($_POST['option']));

	// Delete
	if ($action === 'd') {
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><font color="lime"><b>News deleted!</b></font></div></div>';
		mysql_delete("DELETE FROM `znote_news` WHERE `id`='$id';");
		$cache = new Cache('engine/cache/news');
		$news = fetchAllNews();
		$cache->setContent($news);
		$cache->save();
	}
	// Add news
	if ($action === 'a') {
		// fetch data
		$char_array = user_character_list($user_data['id']);
		?>

		<script src="engine/js/nicedit.js" type="text/javascript"></script>
		<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
		<form action="" method="post">
		<div class="donate-page__desc" style="color:white">
			<input type="hidden" name="option" value="i!0">
			Select character:  <select name="selected_char" class="btn yellow header__info-button">
			<?php
			$count = 0;
			if ($char_array !== false) {
				foreach ($char_array as $name) {
					$name = $name['name'];
					$charD = user_character_data(user_character_id($name), 'group_id', 'id');
					if ($charD['group_id'] > 1) {
						echo '<option value="'. user_character_id($name) .'" style="height:80px;font-size:16px">'. $name .'</option>';
						$count++;
					}
				}
			}
			?>
			</select></div>
			<div class="donate-page__desc" style="color:white">
			<input type="text" name="title" value="" placeholder="Title" style="
    background: rgba(14, 9, 8, 0.9);
    border: 1px solid #2d1f1e;
    border-radius: 4px;
    padding: 10px 15px;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #d4c3b8;"> [youtube]wK0w0x62PjA[/youtube]</div> <br />
			<textarea name="text" id="area1" cols="75" rows="10" placeholder="Contents..." style="width: 100%;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;"></textarea><br />
			<div class="donate-page__desc" style="color:white">
			<button type="submit" value="Create News" class="btn yellow flex-cc header__info-button">Create News</button></div>
		</form>

		<?php
		if ($count === 0) echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><font size='6' color='red'>ERROR: NO GMs or Tutors on this account!</font></div></div>";
	}
	// Insert news
	if ($action === 'i') {
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><font color="lime"><b>News created successfully!</b></font></div></div>';
		list($charid, $title, $text) = array((int)$_POST['selected_char'], mysql_znote_escape_string($_POST['title']), mysql_znote_escape_string($_POST['text']));
		$date = time();
		mysql_insert("INSERT INTO `znote_news` (`title`, `text`, `date`, `pid`) VALUES ('$title', '$text', '$date', '$charid');");
		// Reload the cache.
		$cache = new Cache('engine/cache/news');
		$news = fetchAllNews();
		$cache->setContent($news);
		$cache->save();
	}
	// Save
	if ($action === 's') {
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><font color="lime"><b>News successfully updated!</b></font></div></div>';
		list($title, $text) = array(mysql_znote_escape_string($_POST['title']), mysql_znote_escape_string($_POST['text']));
		mysql_update("UPDATE `znote_news` SET `title`='$title',`text`='$text' WHERE `id`='$id';");
		$cache = new Cache('engine/cache/news');
		$news = fetchAllNews();
		$cache->setContent($news);
		$cache->save();
	}
	// Edit
	if ($action === 'e') {
		$news = fetchAllNews();
		$edit = array();
		foreach ($news as $n) if ($n['id'] == $id) $edit = $n;
		?>
		<script src="engine/js/nicedit.js" type="text/javascript"></script>
		<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
		<form action="" method="post">
			<input type="hidden" name="option" value="s!<?php echo $id; ?>">
			<input type="text" name="title" value="<?php echo $edit['title']; ?>" style="background: rgba(14, 9, 8, 0.9);
    border: 1px solid #2d1f1e;
    border-radius: 4px;
    padding: 10px 15px;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #d4c3b8;"><br />
			<textarea name="text" cols="75" rows="10" style="width: 100%;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;"><?php echo $edit['text']; ?></textarea><br />
			<button type="submit" value="Save Changes" class="btn yellow flex-cc header__info-button">Save Changes</button>
		</form>
		<br>
		<p>
			[b]<b>Bold Text</b>[/b]<br>
			[size=5]Size 5 text[/size]<br>
			[img]<a href="https://imgur.com/" target="_BLANK">Direct Image Link</a>[/img]<br>
			[center]Cented Text[/center]<br>
			[link]<a href="https://youtube.com/" target="_BLANK">https://youtube.com/</a>[/link]<br>
			[link=https://youtube.com/]<a href="http://youtube.com/" target="_BLANK">Click to View youtube</a>[/link]<br>
			[color=<font color="green">GREEN</font>]<font color="green">Green Text!</font>[/color]<br>
			[*]* Noted text [/*]
		</p>
		<?php
	}
}

?>
<div class="donate-page__title" style="color:white">Admin News</div><br></br>
<form action="" method="post">
	<input type="hidden" name="option" value="a!0">
	<button type="submit" value="Create new article" class="btn yellow flex-cc header__info-button">Create New Article</button>
</form><br></br>
<?php
// pre stuff
$news = fetchAllNews();
if ($news !== false) {
	?>
	<table id="news">
		<tr class="yellow">
		<td><div class="donate-page__bonuses-title flex-cc">Date:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">By:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:80px">Title:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:60px">Edit:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:60px">Delete:</div></td>
		</tr>
		<?php
		foreach ($news as $n) {
			echo '<tr>';
			echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:0px">'. getClock($n['date'], true) .'</div></td>';
			echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:0px"><a href="characterprofile.php?name='. $n['name'] .'">'. $n['name'] .'</a></div></td>';
			echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:80px">'. $n['title'] .'</div></td>';
			echo '<td>';
			// edit
			?><div  style="padding-left:60px">
			<form action="" method="post">
				<input type="hidden" name="option" value="e!<?php echo $n['id']; ?>">
				<button type="submit" value="Edit" class="btn yellow flex-cc header__info-button">Edit</button>
			</form></div>
			<?php
			echo '</td>';
			echo '<td>';
			// delete
			?><div  style="padding-left:60px">
			<form action="" method="post">
				<input type="hidden" name="option" value="d!<?php echo $n['id']; ?>">
				<button type="submit" value="Delete" class="btn yellow flex-cc header__info-button">Delete</button>
			</form></div>
			<?php
			echo '</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php
}
include 'layout/overall/footer_login.php'; ?>
