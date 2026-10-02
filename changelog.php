<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
$updateCache = false;
if (user_logged_in()) {
	if (is_admin($user_data)) {
		// variables
		$status = true;
		if (isset($_POST['changelogId'])) $changelogId = (int)$_POST['changelogId'];
		else $status = false;
		if (isset($_POST['changelogText'])) $changelogText = getValue($_POST['changelogText']);
		else $status = false;

		if (isset($_POST['action'])) $action = (int)$_POST['action'];
		else $action = 0;
		// POST delete
		if (isset($_POST['delete'])) {
			$delete = isset($_POST['delete']) ? (int)$_POST['delete'] : 0;
			if ($delete && $action == 1) {
				mysql_delete("DELETE FROM `znote_changelog` WHERE `id`='$delete' LIMIT 1;");
				echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px'><div class='desc-changelog__block flex-ss'><h2>Changelog message deleted!</h2></div></div>";
				$updateCache = true;
			}
		} else {
			if ($status) {
				// POST update
				if ($changelogId > 0) {
					mysql_update("UPDATE `znote_changelog` SET `text`='$changelogText' WHERE `id`='$changelogId' LIMIT 1;");
					echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px'><div class='desc-changelog__block flex-ss'><h2>Changelog message updated!</h2></div></div>";
					$updateCache = true;
				} else {
					// POST create
					$time = time();
					mysql_insert("INSERT INTO `znote_changelog` (`text`, `time`, `report_id`, `status`) VALUES ('$changelogText', '$time', '0', '35');");
					echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px'><div class='desc-changelog__block flex-ss'><h2>Changelog message created!</h2></div></div>";
					$updateCache = true;
				}
			}
		}
		if ($action === 2) {
			$old = mysql_select_single("SELECT `text` FROM `znote_changelog` WHERE `id`='$changelogId' LIMIT 1;");
		}
		// HTML to create or update
		?>
		<div class="donate-page__title" style="color:white">Add/Update Changelog</div><br></br>
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
		<center>
		<form action="" method="POST">
			<input name="changelogId" type="hidden" value="<?php echo ($action === 2) ? $changelogId : 0; ?>">
			<textarea rows="7" cols="40" maxlength="254" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;" name="changelogText"><?php echo ($action === 2) ? $old['text'] : ''; ?></textarea><br>
			<button type="submit" class="btn yellow flex-cc header__info-button" value="Add or update changelog">Add or Update Changelog</button>
		</form>
	</center>
	</div></div>
		<?php
	}
}
?><br></br>

<div class="donate-page__title" style="color:white">Changelog</div><br></br>
<?php
$cache = new Cache('engine/cache/changelog');
$cache->useMemory(false);
if ($updateCache === true) {
	$changelogs = mysql_select_multi("SELECT `id`, `text`, `time`, `report_id`, `status` FROM `znote_changelog` ORDER BY `id` DESC;");

	$cache->setContent($changelogs);
	$cache->save();
} else {
	$changelogs = $cache->load();
}
if (isset($changelogs) && !empty($changelogs) && $changelogs !== false) {
	?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
	<table id="changelogTable">
		<tr class="yellow">
			<td><div class="donate-page__bonuses-title flex-cc">Changelogs</div></td>
			<?php
			if (user_logged_in())
				if (is_admin($user_data)) {
					echo "<td><div class='donate-page__bonuses-title flex-cc'>Delete</div></td><td><div class='donate-page__bonuses-title flex-cc'>Update</div></td>";
				}
			?>
		</tr>
		<?php
		foreach ($changelogs as $changelog) {
		?>
		<tr>
			<td><b><?php echo getClock((isset($changelog['time'])) ? $changelog['time'] : 0, true, true); ?></b><br><?php echo $changelog['text']; ?></td>
			<?php
			if (user_logged_in())
				if (is_admin($user_data)) {
					?>
					<td>
						<form action="" method="POST">
							<input name="delete" type="hidden" value="<?php echo $changelog['id']; ?>">
							<input name="action" type="hidden" value="1">
							<button type="submit" value="DELETE" class="btn yellow flex-cc header__info-button">Delete</button>
						</form>
					</td>
					<td>
						<form action="" method="POST">
							<input name="changelogId" type="hidden" value="<?php echo $changelog['id']; ?>">
							<input name="action" type="hidden" value="2">
							<button type="submit" value="UPDATE" class="btn yellow flex-cc header__info-button">Update</button>
						</form>
					</td>
					<?php
				}
			?>
		</tr>
		<?php
		}
		?>
	</table></div></div>
	<?php
} else {
	?>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
	<h2>Currently no change logs submitted.</h2>
</div></div>
	<?php
}
include 'layout/overall/footer_login.php'; ?>
