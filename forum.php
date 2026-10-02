
<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
protect_page();
error_reporting(E_ALL ^ E_NOTICE);
if (!$config['forum']['enabled']) admin_only($user_data);
/*  -------------------------------
	---		Znote AAC forum 	---
	-------------------------------
	Created by Znote.
	Version 1.4.

	Changelog (1.0 --> 1.2):
	- Updated to the new date/clock time system
	- Bootstrap design support.

	Changelog (1.2 --> 1.3):
	- Show character outfit as avatar
	- Show in-game position

	Changelog (1.3 -> 1.4):
	- Fix SQL query error when editing Board name.
*/
// BBCODE support:
function TransformToBBCode($string) {
	$tags = array(
		'[center]{$1}[/center]' => '<center>$1</center>',
		'[b]{$1}[/b]' => '<b>$1</b>',
		'[img]{$1}[/img]'    => '<a href="$1" target="_BLANK"><img src="$1" alt="image" style="width: 100%"></a>',
		'[link]{$1}[/link]'    => '<a href="$1">$1</a>',
		'[link={$1}]{$2}[/link]'   => '<a href="$1" target="_BLANK">$2</a>',
		'[url={$1}]{$2}[/url]'   => '<a href="$1" target="_BLANK">$2</a>',
		'[color={$1}]{$2}[/color]' => '<font color="$1">$2</font>',
		'[*]{$1}[/*]' => '<li>$1</li>',
		'[youtube]{$1}[/youtube]' => '<div class="youtube"><div class="aspectratio"><iframe src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div></div>',
	);

	foreach ($tags as $tag => $value) {
		$code = preg_replace('/placeholder([0-9]+)/', '(.*?)', preg_quote(preg_replace('/\{\$([0-9]+)\}/', 'placeholder$1', $tag), '/'));
		$string = preg_replace('/'.$code.'/i', $value, $string);
		if (strpos($string, "<a href=") !== false) {
			if (strpos($string, "http") === false) {
				$string = substr_replace($string, "//", 9, 0);
			}
		}
	}

	return $string;
}
Function PlayerHaveAccess($yourChars, $playerName){
	$access = false;
	foreach($yourChars as $char) {
		if ($char['name'] == $playerName) $access = true;
	}
	return $access;
}

// Start page init
$admin = is_admin($user_data);
if ($admin) $yourChars = mysql_select_multi("SELECT `id`, `name`, `group_id` FROM `players` WHERE `level`>='1' AND `account_id`='". $user_data['id'] ."';");
else $yourChars = mysql_select_multi("SELECT `id`, `name`, `group_id` FROM `players` WHERE `level`>='". $config['forum']['level'] ."' AND `account_id`='". $user_data['id'] ."';");
if (!$yourChars) $yourChars = array();
$charCount = count($yourChars);
$yourAccess = accountAccess($user_data['id'], $config['ServerEngine']);
if ($admin) {
	if (!empty($_POST)) {
		$guilds = mysql_select_multi("SELECT `id`, `name` FROM `guilds` ORDER BY `name`;");
		$guilds[] = array('id' => '0', 'name' => 'No guild');
	}
	$yourAccess = 100;
}

// Your characters, indexed by char_id
$charData = array();
foreach ($yourChars as $char) {
	$charData[$char['id']] = $char;
	if (get_character_guild_rank($char['id']) > 0) {
		$guild = get_player_guild_data($char['id']);
		$charData[$char['id']]['guild'] = $guild['guild_id'];
		$charData[$char['id']]['guild_rank'] = $guild['rank_level'];
	} else $charData[$char['id']]['guild'] = '0';
}
$cooldownw = array(
	$user_znote_data['cooldown'],
	time(),
	$user_znote_data['cooldown'] - time()
	);

/////////////////
// Guild Leader & admin
$leader = false;
foreach($charData as $char) {
	if ($char['guild'] > 0 && $char['guild_rank'] == 3) $leader = true;
}
if ($admin && !empty($_POST) || $leader && !empty($_POST)) {
	$admin_thread_delete = getValue($_POST['admin_thread_delete']);
	$admin_thread_close = getValue($_POST['admin_thread_close']);
	$admin_thread_open = getValue($_POST['admin_thread_open']);
	$admin_thread_sticky = getValue($_POST['admin_thread_sticky']);
	$admin_thread_unstick = getValue($_POST['admin_thread_unstick']);
	$admin_thread_id = getValue($_POST['admin_thread_id']);

	// delete thread
	if ($admin_thread_delete !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;

		if ($access) {
			// Delete all associated posts
			mysql_delete("DELETE FROM `znote_forum_posts` WHERE `thread_id`='$admin_thread_id';");
			// Delete thread itself
			mysql_delete("DELETE FROM `znote_forum_threads` WHERE `id`='$admin_thread_id' LIMIT 1;");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Thread and all associated posts deleted.</h1></div></div>';
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission denied.</font></b></p></div></div>';
	}

	// Close thread
	if ($admin_thread_close !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `closed`='1' WHERE `id`='$admin_thread_id' LIMIT 1;");
			//die("UPDATE `znote_forum_threads` SET `closed`='1' WHERE `id`='$admin_thread_id' LIMIT 1;");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Thread has been closed.</h1></div></div>';
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission denied.</font></b></p></div></div>';
	}

	// open thread
	if ($admin_thread_open !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `closed`='0' WHERE `id`='$admin_thread_id' LIMIT 1;");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Thread has been opened.</h1></div></div>';
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission denied.</font></b></p></div></div>';
	}

	// stick thread
	if ($admin_thread_sticky !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `sticky`='1' WHERE `id`='$admin_thread_id' LIMIT 1;");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Thread has been sticked.</h1></div></div>';
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission denied.</font></b></p></div></div>';
	}

	// unstick thread
	if ($admin_thread_unstick !== false) {
		$admin_thread_id = (int)$admin_thread_id;
		$access = false;
		if (!$admin) {
			$thread = mysql_select_single("SELECT `forum_id` FROM `znote_forum_threads` WHERE `id`='$admin_thread_id';");
			$forum = mysql_select_single("SELECT `guild_id` FROM `znote_forum` WHERE `id`='". $thread['forum_id'] ."';");
			foreach($charData as $char) if ($char['guild'] == $forum['guild_id'] && $char['guild_rank'] == 3) $access = true;
		} else $access = true;
		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `sticky`='0' WHERE `id`='$admin_thread_id' LIMIT 1;");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Thread has been unsticked.</h1></div></div>';
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission denied.</font></b></p></div></div>';
	}
}

/////////////////
// ADMIN FUNCT
if ($admin && !empty($_POST)) {
	$admin_post_id = getValue($_POST['admin_post_id']);
	$admin_post_delete = getValue($_POST['admin_post_delete']);

	$admin_category_delete = getValue($_POST['admin_category_delete']);
	$admin_category_edit = getValue($_POST['admin_category_edit']);
	$admin_category_id = getValue($_POST['admin_category_id']);

	$admin_update_category = getValue($_POST['admin_update_category']);
	$admin_category_name = getValue($_POST['admin_category_name']);

	$admin_category_access = getValue($_POST['admin_category_access']);
	$admin_category_closed = getValue($_POST['admin_category_closed']);
	$admin_category_hidden = getValue($_POST['admin_category_hidden']);
	$admin_category_guild_id = getValue($_POST['admin_category_guild_id']);

	if ($admin_category_access === false) $admin_category_access = 0;
	if ($admin_category_closed === false) $admin_category_closed = 0;
	if ($admin_category_hidden === false) $admin_category_hidden = 0;
	if ($admin_category_guild_id === false) $admin_category_guild_id = 0;

	$admin_board_create_name = getValue($_POST['admin_board_create_name']);
	$admin_board_create_access = getValue($_POST['admin_board_create_access']);
	$admin_board_create_closed = getValue($_POST['admin_board_create_closed']);
	$admin_board_create_hidden = getValue($_POST['admin_board_create_hidden']);
	$admin_board_create_guild_id = getValue($_POST['admin_board_create_guild_id']);

	if ($admin_board_create_access === false) $admin_board_create_access = 0;
	if ($admin_board_create_closed === false) $admin_board_create_closed = 0;
	if ($admin_board_create_hidden === false) $admin_board_create_hidden = 0;
	if ($admin_board_create_guild_id === false) $admin_board_create_guild_id = 0;

	// Create board
	if ($admin_board_create_name !== false) {

		// Insert data
		mysql_insert("INSERT INTO `znote_forum` (`name`, `access`, `closed`, `hidden`, `guild_id`)
			VALUES ('$admin_board_create_name',
				'$admin_board_create_access',
				'$admin_board_create_closed',
				'$admin_board_create_hidden',
				'$admin_board_create_guild_id');");
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Board has been created.</h1></div></div>';
	}

	//////////////////
	// update category
	if ($admin_update_category !== false) {
		$admin_category_id = (int)$admin_category_id;

		// Update the category
		mysql_update("UPDATE `znote_forum` SET
			`name`='$admin_category_name',
			`access`='$admin_category_access',
			`closed`='$admin_category_closed',
			`hidden`='$admin_category_hidden',
			`guild_id`='$admin_category_guild_id'
			WHERE `id`='$admin_category_id' LIMIT 1;");
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Board has been updated successfully.</h1></div></div>';
	}

	//////////////////
	// edit category
	if ($admin_category_edit !== false) {
		$admin_category_id = (int)$admin_category_id;
		$category = mysql_select_single("SELECT `id`, `name`, `access`, `closed`, `hidden`, `guild_id`
			FROM `znote_forum` WHERE `id`='$admin_category_id' LIMIT 1;");
		if ($category !== false) {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
			<div style="margin-left:auto;margin-right:auto">
			<form action="" method="post">
				<input type="hidden" name="admin_category_id" value="<?php echo $category['id']; ?>">
				<table class="updateTable table table-striped">
					<tr>
						<td><div class="hts__block-files-title"><label for="admin_category_name">Board name:</label></div></td>
						<td><input name="admin_category_name" value="<?php echo $category['name']; ?>" class="span12" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;"></td>

					</tr>
					<tr>
						<td><div class="hts__block-files-title"><label for="admin_category_access">Required Access:</label></div></td>
						<td>
							<select name="admin_category_access" class="span12" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
								<?php
								foreach($config['ingame_positions'] as $access => $name) {
									if ($access == $category['access']) echo "<option value='$access' selected>$name</option>";
									else echo "<option value='$access'>$name</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><div class="hts__block-files-title"><label for="admin_category_closed">Closed:</label></div></td>
						<td>
							<select name="admin_category_closed" class="span12" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
								<?php
								if ($category['closed'] == 1) echo '<option value="1" selected>Yes</option>';
								else echo '<option value="1">Yes</option>';
								if ($category['closed'] == 0) echo '<option value="0" selected>No</option>';
								else echo '<option value="0">No</option>';
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><div class="hts__block-files-title"><label for="admin_category_hidden">Hidden:</label></div></td>
						<td>
							<select name="admin_category_hidden" class="span12" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
								<?php
								if ($category['hidden'] == 1) echo '<option value="1" selected>Yes</option>';
								else echo '<option value="1">Yes</option>';
								if ($category['hidden'] == 0) echo '<option value="0" selected>No</option>';
								else echo '<option value="0">No</option>';
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td><div class="hts__block-files-title"><label for="admin_category_guild_id">Guild id:</label></div></td>
						<td>
							<select name="admin_category_guild_id" class="span12" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
								<?php foreach($guilds as $guild) {
									if ($category['guild_id'] == $guild['id']) echo "<option value='". $guild['id'] ."' selected>". $guild['name'] ."</option>";
									else echo "<option value='". $guild['id'] ."'>". $guild['name'] ."</option>";
								} ?>
							</select> 
						</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="admin_update_category" value="Update Board" style="width: 100%; height: 30px;" class="btn yellow flex-cc header__info-button"></td>
					</tr>
				</table>
			</form></div></div></div>
			<?php
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h2>Category not found.</h2></div></div>';

	}

	// delete category
	if ($admin_category_delete !== false) {
		$admin_category_id = (int)$admin_category_id;

		// find all threads in category
		$threads = mysql_select_multi("SELECT `id` FROM `znote_forum_threads` WHERE `forum_id`='$admin_category_id';");

		// Then loop through all threads, and delete all associated posts:
		foreach($threads as $thread) {
			mysql_delete("DELETE FROM `znote_forum_posts` WHERE `thread_id`='". $thread['id'] ."';");
		}
		// Then delete all threads
		mysql_delete("DELETE FROM `znote_forum_threads` WHERE `forum_id`='$admin_category_id';");
		// Then delete the category
		mysql_delete("DELETE FROM `znote_forum` WHERE `id`='$admin_category_id' LIMIT 1;");
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Board, associated threads and all their associated posts deleted.</h1></div></div>';
	}

	// delete post
	if ($admin_post_delete !== false) {
		$admin_post_id = (int)$admin_post_id;

		// Delete the post
		mysql_delete("DELETE FROM `znote_forum_posts` WHERE `id`='$admin_post_id' LIMIT 1;");
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Post has been deleted.</h1></div></div>';
	}
}
// End admin function

// Fetching get values
if (!empty($_GET)) {
	$getCat = getValue($_GET['cat']);
	$getForum = getValue($_GET['forum']);
	$getThread = getValue($_GET['thread']);

	$new_thread_category = getValue($_POST['new_thread_category']);
	$new_thread_cid = getValue($_POST['new_thread_cid']);

	$create_thread_cid = getValue($_POST['create_thread_cid']);
	$create_thread_title = getValue($_POST['create_thread_title']);
	$create_thread_text = getValue($_POST['create_thread_text']);
	$create_thread_category = getValue($_POST['create_thread_category']);

	$update_thread_id = getValue($_POST['update_thread_id']);
	$update_thread_title = getValue($_POST['update_thread_title']);
	$update_thread_text = getValue($_POST['update_thread_text']);

	$edit_thread = getValue($_POST['edit_thread']);
	$edit_thread_id = getValue($_POST['edit_thread_id']);

	$reply_thread = getValue($_POST['reply_thread']);
	$reply_text = getValue($_POST['reply_text']);
	$reply_cid = getValue($_POST['reply_cid']);

	$edit_post = getValue($_POST['edit_post']);
	$edit_post_id = getValue($_POST['edit_post_id']);

	$update_post_id = getValue($_POST['update_post_id']);
	$update_post_text = getValue($_POST['update_post_text']);

	/////////////////////
	// When you are POSTING in an existing thread
	if ($reply_thread !== false && $reply_text !== false && $reply_cid !== false) {
		$reply_cid = (int)$reply_cid;

		if ($user_znote_data['cooldown'] < time()) {
			user_update_znote_account(array('cooldown'=>(time() + $config['forum']['cooldownPost'])));

			$thread = mysql_select_single("SELECT `closed` FROM `znote_forum_threads` WHERE `id`='$reply_thread' LIMIT 1;");

			if ($thread['closed'] == 1 && $admin === false) $access = false;
			else $access = true;

			if ($access) {
				mysql_insert("INSERT INTO `znote_forum_posts` (`thread_id`, `player_id`, `player_name`, `text`, `created`, `updated`) VALUES ('$reply_thread', '$reply_cid', '". $charData[$reply_cid]['name'] ."', '$reply_text', '". time() ."', '". time() ."');");
				if ($config['forum']['newPostsBumpThreads']) mysql_update("UPDATE `znote_forum_threads` SET `updated`='". time() ."' WHERE `id`='$reply_thread';");
			} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">You don\'t have permission to post on this thread. [Thread: Closed]</font></b></p></div></div>';
		} else {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
				<font class="forumCooldown" color="red">Antispam: You need to wait <?php echo ($user_znote_data['cooldown'] - time()); ?> seconds before you can create or post.</font></div></div>
			<?php
		}
	}

	/////////////////////
	// When you ARE creating new thread
	if ($create_thread_cid !== false && $create_thread_title !== false && $create_thread_text !== false && $create_thread_category !== false) {
		if ($user_znote_data['cooldown'] < time()) {
			user_update_znote_account(array('cooldown'=>(time() + $config['forum']['cooldownCreate'])));

			$category = mysql_select_single("SELECT `access`, `closed`, `guild_id` FROM `znote_forum` WHERE `id`='$create_thread_category' LIMIT 1;");
			if ($category !== false) {
				$access = true;
				if (!$admin) {
					if ($category['access'] > $yourAccess) $access = false;
					if ($category['guild_id'] > 0) {
						$status = false;
						foreach($charData as $char) {
							if ($char['guild'] == $category['guild_id']) $status = true;
						}
						if (!$status) $access = false;
					}
					if ($category['closed'] > 0) $access = false;
				}

				if ($access) {
					mysql_insert("INSERT INTO `znote_forum_threads`
						(`forum_id`, `player_id`, `player_name`, `title`, `text`, `created`, `updated`, `sticky`, `hidden`, `closed`)
						VALUES (
							'$create_thread_category',
							'$create_thread_cid',
							'". $charData[$create_thread_cid]['name'] ."',
							'$create_thread_title',
							'$create_thread_text',
							'". time() ."',
							'". time() ."',
							'0', '0', '0');");
					SendGet(array('cat'=>$create_thread_category), 'forum.php');
				} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission to create thread denied.</font></b></p></div></div>';
			} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Category does not exist.</div></div>';
		} else {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
				<font class="forumCooldown" color="red">Antispam: You need to wait <?php echo ($user_znote_data['cooldown'] - time()); ?> seconds before you can create or post.</font></div></div>
			<?php
		}
	}

	/////////////////////
	// When you ARE updating post
	if ($update_post_id !== false && $update_post_text !== false) {
		// Fetch the post data
		$post = mysql_select_single("SELECT `id`, `player_name`, `text`, `thread_id` FROM `znote_forum_posts` WHERE `id`='$update_post_id' LIMIT 1;");
		$thread = mysql_select_single("SELECT `closed` FROM `znote_forum_threads` WHERE `id`='". $post['thread_id'] ."' LIMIT 1;");

		// Verify access
		$access = PlayerHaveAccess($yourChars, $post['player_name']);
		if ($thread !== false && $thread['closed'] == 1 && $admin === false) $access = false;
		if ($admin) $access = true;
		//if ($thread === false) $access = false;

		if ($access) {
			mysql_update("UPDATE `znote_forum_posts` SET `text`='$update_post_text', `updated`='". time() ."' WHERE `id`='$update_post_id';");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>post has been updated.</h1></div></div>';
		} else echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><p><font color='red'>Your permission to edit this post has been denied.</font></p></div></div>";
	}

	/////////////////////
	// When you ARE updating thread
	if ($update_thread_id !== false && $update_thread_title !== false && $update_thread_text !== false) {
		// Fetch the thread data
		$thread = mysql_select_single("SELECT `id`, `player_name`, `title`, `text`, `closed` FROM `znote_forum_threads` WHERE `id`='$update_thread_id' LIMIT 1;");

		// Verify access
		$access = PlayerHaveAccess($yourChars, $thread['player_name']);
		if ($thread['closed'] == 1 && $admin === false) $access = false;
		if ($admin) $access = true;

		if ($access) {
			mysql_update("UPDATE `znote_forum_threads` SET `title`='$update_thread_title', `text`='$update_thread_text' WHERE `id`='$update_thread_id';");
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><h1>Thread has been updated.</h1></div></div>';
		} else echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><p><font color='red'>Your permission to edit this thread has been denied.</font></p></div></div>";
	}

	/////////////////////
	// When you want to edit a post
	if ($edit_post_id !== false && $edit_post !== false) {
		// Fetch the post data
		$post = mysql_select_single("SELECT `id`, `thread_id`, `text`, `player_name` FROM `znote_forum_posts` WHERE `id`='$edit_post_id' LIMIT 1;");
		$thread = mysql_select_single("SELECT `closed` FROM `znote_forum_threads` WHERE `id`='". $post['thread_id'] ."' LIMIT 1;");
		// Verify access
		$access = PlayerHaveAccess($yourChars, $post['player_name']);
		if ($thread['closed'] == 1 && $admin === false) $access = false;
		if ($admin) $access = true;

		if ($access) {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
			<h1>Edit Post</h1>
			<form type="" method="post">
				<input name="update_post_id" type="hidden" value="<?php echo $post['id']; ?>">
				<textarea name="update_post_text" style="width: 610px; height: 300px"><?php echo $post['text']; ?></textarea><br>
				<input type="submit" value="Update Post" class="btn yellow flex-cc header__info-button">
			</form></div></div>
			<?php
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">You don\'t have permission to edit this post.</font></b></p></div></div>';
	} else

	/////////////////////
	// When you want to edit a thread
	if ($edit_thread_id !== false && $edit_thread !== false) {
		// Fetch the thread data
		$thread = mysql_select_single("SELECT `id`, `title`, `text`, `player_name`, `closed` FROM `znote_forum_threads` WHERE `id`='$edit_thread_id' LIMIT 1;");

		$access = PlayerHaveAccess($yourChars, $thread['player_name']);
		if ($thread['closed'] == 1) $access = false;
		if ($admin) $access = true;

		if ($access) {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
			<h1>Edit Thread</h1>
			<form type="" method="post">
				<input name="update_thread_id" type="hidden" value="<?php echo $thread['id']; ?>">
				<input name="update_thread_title" type="text" value="<?php echo $thread['title']; ?>" style="width: 500px;"><br><br>
				<textarea name="update_thread_text" style="width: 610px; height: 300px"><?php echo $thread['text']; ?></textarea><br>
				<input type="submit" value="Update Thread" class="btn yellow flex-cc header__info-button">
			</form></div></div>
			<?php
		} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Edit access denied.</font></b></p></div></div>';
	} else

	/////////////////////
	// When you want to view a thread
	if ($getThread !== false) {
		$getThread = (int)$getThread;
		$threadData = mysql_select_single("SELECT `id`, `forum_id`, `player_id`, `player_name`, `title`, `text`, `created`, `updated`, `sticky`, `hidden`, `closed` FROM `znote_forum_threads` WHERE `id`='$getThread' LIMIT 1;");

		if ($threadData !== false) {

			$category = mysql_select_single("SELECT `hidden`, `access`, `guild_id` FROM `znote_forum` WHERE `id`='". $threadData['forum_id'] ."' LIMIT 1;");
			if ($category === false) die("Thread category does not exist.");

			$access = true;
			$leader = false;
			if ($category['hidden'] == 1 || $category['access'] > 1 || $category['guild_id'] > 0) {
				$access = false;
				if ($category['hidden'] == 1) $access = PlayerHaveAccess($yourChars, $threadData['player_name']);
				if ($category['access'] > 1 && $yourAccess >= $category['access']) $access = true;
				foreach($charData as $char) {
					if ($category['guild_id'] == $char['guild']) $access = true;
					if ($char['guild_rank'] == 3) $leader = true;
				}
				if ($admin) $access = true;
			}


			if ($access) {
				$threadPlayer = ($config['forum']['outfit_avatars'] || $config['forum']['player_position']) ? mysql_select_single("SELECT `id`, `group_id`, `sex`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons` FROM `players` WHERE `id`='".$threadData['player_id']."';") : false;
				?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px">
				<div class="desc-changelog__block flex-ss" style="">
				<font>LinkMap: <a href="forum.php">Forum</a> - <a href="?cat=<?php echo $getCat; ?>"><?php echo $getForum; ?></a></font><br></br>

				<div class="donate-page__title" style="color:white">Viewing Thread <?php echo "<a href='?forum=". $getForum ."&cat=". $getCat ."&thread=". $threadData['id'] ."'>". $threadData['title'] ."</a>"; ?></div><br></br>
				<table class="znoteTable ThreadTable table table-striped">
					<tr class="yellow">
						<th<?php if ($threadPlayer !== false) echo ' colspan="2"'; ?>><div class="donate-page__bonuses-title flex-cc">
							<?php
							echo getClock($threadData['created'], true);
							if ($threadPlayer === false): ?>
								 - Created by:
								<?php
						 		echo "<a href='characterprofile.php?name=". $threadData['player_name'] ."'>". $threadData['player_name'] ."</a>";
					 		endif;
					 		?>
						</div></th>
					</tr>
					<tr>
						<?php if ($threadPlayer !== false): ?>
						<td class="avatar"><div class="donate-page__bonuses-title flex-cc">
							<a href='characterprofile.php?name=<?php echo $threadData['player_name']; ?>'><?php echo $threadData['player_name']; ?></a>
							<?php if ($config['forum']['outfit_avatars']): ?>
							<br><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $threadPlayer['looktype']; ?>&addons=<?php echo $threadPlayer['lookaddons']; ?>&head=<?php echo $threadPlayer['lookhead']; ?>&body=<?php echo $threadPlayer['lookbody']; ?>&legs=<?php echo $threadPlayer['looklegs']; ?>&feet=<?php echo $threadPlayer['lookfeet']; ?>" alt="img">
							<?php endif; ?>
							<?php if ($config['forum']['player_position']): ?>
							<br><span><?php echo group_id_to_name($threadPlayer['group_id']); ?></span>
							<?php endif; ?>
							</div></td>
						<?php endif; ?>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">
							<p><?php echo nl2br(TransformToBBCode($threadData['text'])); ?></p>
							</div></td>
					</tr>
				</table></div></div>
				<?php
				if ($admin || $leader) {
					// PlayerHaveAccess($yourChars, $thread['player_name']) ||
					// $yourChars
					?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
					<div style="margin-left:auto;margin-right:auto">
					<table class="adminTable table">
						<tr>
							<td>
								<form action="" method="post">
									<input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
									<input type="submit" name="admin_thread_delete" value="Delete Thread" class="btn yellow flex-cc header__info-button">
								</form>
							</td>
							<td>
								<?php if ($threadData['closed'] == 0) { ?>
									<form action="" method="post">
										<input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
										<input type="submit" name="admin_thread_close" value="Close Thread" class="btn yellow flex-cc header__info-button">
									</form>
								<?php } else { ?>
									<form action="" method="post">
										<input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
										<input type="submit" name="admin_thread_open" value="Open Thread" class="btn yellow flex-cc header__info-button">
									</form>
								<?php } ?>
							</td>
							<td>
								<?php if ($threadData['sticky'] == 0) { ?>
									<form action="" method="post">
										<input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
										<input type="submit" name="admin_thread_sticky" value="Stick thread" class="btn yellow flex-cc header__info-button">
									</form>
								<?php } else { ?>
									<form action="" method="post">
										<input type="hidden" name="admin_thread_id" value="<?php echo $threadData['id']; ?>">
										<input type="submit" name="admin_thread_unstick" value="Unstick thread" class="btn yellow flex-cc header__info-button">
									</form>
								<?php } ?>
							</td>
							<td>
								<form action="" method="post">
									<input type="hidden" name="edit_thread_id" value="<?php echo $threadData['id']; ?>">
									<input type="submit" name="edit_thread" value="Edit Thread" class="btn yellow flex-cc header__info-button">
								</form>
							</td>
						</tr>
					</table></div></div></div>
					<?php
				} else {
					if ($threadData['closed'] == 0 && PlayerHaveAccess($yourChars, $threadData['player_name'])) {
						?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
						<table class="editThread">
							<tr>
								<td>
									<form action="" method="post">
										<input type="hidden" name="edit_thread_id" value="<?php echo $threadData['id']; ?>">
										<input type="submit" name="edit_thread" value="Edit Thread" class="btn yellow flex-cc header__info-button">
									</form>
								</td>
							</tr>
						</table></div></div>
						<?php
					}
				}
				?>
				<?php
				// Display replies... (copy table above and edit each post)
				$posts = mysql_select_multi("SELECT `id`, `player_id`, `player_name`, `text`, `created`, `updated` FROM `znote_forum_posts` WHERE `thread_id`='". $threadData['id'] ."' ORDER BY `created`;");
				if ($posts !== false) {
					// Load extra data (like outfit avatars?)
					$players = array();
					$extra = false;
					if ($config['forum']['outfit_avatars'] || $config['forum']['player_position']) {
						$extra = true;

						foreach($posts as $post)
							if (!isset($players[$post['player_id']]))
								$players[$post['player_id']] = array();

						$sql_players = mysql_select_multi("SELECT `id`, `group_id`, `sex`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons` FROM `players` WHERE `id` IN (".implode(',', array_keys($players)).");");

						foreach ($sql_players as $player)
							$players[$player['id']] = $player;

					}

					foreach($posts as $post) {
						?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
						
						<table class="znoteTable ThreadTable table table-striped">
							<tr class="yellow">
								<th<?php if ($extra) echo ' colspan="2"'; ?>><div class="donate-page__bonuses-title flex-cc">
									<?php echo getClock($post['created'], true);
									if (!$extra): ?>
										 - Posted by:
										 <?php echo "<a href='characterprofile.php?name=". $post['player_name'] ."'>". $post['player_name'] ."</a>";
									 endif; ?>
								</div></th>
							</tr>
							<tr>
								<?php if ($extra): ?>
								<td class="avatar"><div class="donate-page__bonuses-title flex-cc">
									<a href='characterprofile.php?name=<?php echo $post['player_name']; ?>'><?php echo $post['player_name']; ?></a>
									<?php if ($config['forum']['outfit_avatars']): ?>
									<br><img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $players[$post['player_id']]['looktype']; ?>&addons=<?php echo $players[$post['player_id']]['lookaddons']; ?>&head=<?php echo $players[$post['player_id']]['lookhead']; ?>&body=<?php echo $players[$post['player_id']]['lookbody']; ?>&legs=<?php echo $players[$post['player_id']]['looklegs']; ?>&feet=<?php echo $players[$post['player_id']]['lookfeet']; ?>" alt="img">
									<?php endif; ?>
									<?php if ($config['forum']['player_position']): ?>
									<br><span><?php echo group_id_to_name($players[$post['player_id']]['group_id']); ?></span>
									<?php endif; ?>
									</div></td>
								<?php endif; ?>
								<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">
									<p><?php echo nl2br(TransformToBBCode($post['text'])); ?></p>
									</div></td>
							</tr>
						</table></div></div>
						<?php
						if (PlayerHaveAccess($yourChars, $post['player_name']) || $admin) {
							if ($admin) {
								?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
								<div style="margin-left:auto;margin-right:auto"><center>
								<form action="" method="post" class="postButton">
									<input type="hidden" name="admin_post_id" value="<?php echo $post['id']; ?>">
									<input type="submit" name="admin_post_delete" value="Delete Post" class="btn yellow flex-cc header__info-button">
								</form>
								<?php
							}
							if ($threadData['closed'] == 0 || $admin) {
								?>
								<form action="" method="post" class="postButton">
									<input type="hidden" name="edit_post_id" value="<?php echo $post['id']; ?>">
									<input type="submit" name="edit_post" value="Edit Post" class="btn yellow flex-cc header__info-button">
								</form></center></div></div></div>
								<?php
							}
						}
					}
				}

				// Quick Reply
				if ($charCount > 0) {
					if ($threadData['closed'] == 0 || $yourAccess > 3) {
						?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
						<form action="" method="post">
							<input name="reply_thread" type="hidden" value="<?php echo $threadData['id']; ?>"><br>
							<div style="margin-left:auto;margin-right:auto">
							<div class="donate-page__bonuses-title"><center><b>[b]Bold Text[/b]</b>, [img]<a href="https://imgur.com/">Direct Image Link</a>[/img], [center]Centered Text[/center], [link]<a href="https://youtube.com/" target="_BLANK">https://youtube.com/</a>[/link], <br></br>[color=<font color="green">GREEN</font>]<font color="green">Green Text!</font>[/color], [*] - Dotted [/*]</center</div><br>
							<textarea class="forumReply" name="reply_text" style="width:600px;height:150px;font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;"></textarea><br>
							<select name="reply_cid" multiple="multiple" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;width:200px">
								<?php
								foreach($yourChars as $char) {
									echo "<option value='". $char['id'] ."'>". $char['name'] ."</option>";
								}
								?>
							</select>
							<button name="" type="submit" value="Post Reply" class="btn yellow flex-cc header__info-button">Post Reply</button></center></div>
						</form></div></div>
						<?php
					} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b>You don\'t have permission to post on this thread. [Thread: Closed]</b></p></div></div>';
				} else {
					?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p>You must have a character on your account that is level <?php echo $config['forum']['level']; ?>+ to reply to this thread.</p></div></div><?php
				}
			} else echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><p><font color='red'>Your permission to access this thread has been denied.</font></p></div></div>";
		} else {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
			<h1>Thread unavailable</h1>
			<p>Thread is unavailable for you, or do not exist any more.</div></div>
				<?php
				if ($_GET['cat'] > 0 && !empty($_GET['forum'])) {
					$tmpCat = getValue($_GET['cat']);
					$tmpCatName = getValue($_GET['forum']);
					?>
					<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><br><a href="forum.php?forum=<?php echo $tmpCatName; ?>&cat=<?php echo $tmpCat; ?>">Go back to: <?php echo $tmpCatName; ?></a></p></div></div>
					<?php
				} else {
					?>
					<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><br><a href="forum.php">Go back to Forum</a></p></div></div>
					<?php
				}
				?>
			<?php
		}

	} else

	/////////////////////
	// When you want to create a new thread
	if ($new_thread_category !== false && $new_thread_cid !== false) {
		// Verify we got access to this category
		$category = mysql_select_single("SELECT `access`, `closed`, `guild_id` FROM `znote_forum` WHERE `id`='$new_thread_category' LIMIT 1;");
		if ($category !== false) {
			$access = true;
			if (!$admin) {
				if ($category['access'] > $yourAccess) $access = false;
				if ($category['guild_id'] > 0) {
					$status = false;
					foreach($charData as $char) {
						if ($char['guild'] == $category['guild_id']) $status = true;
					}
					if (!$status) $access = false;
				}
				if ($category['closed'] > 0) $access = false;
			}

			if ($access) {
				?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
				<div class="donate-page__title" style="color:white">Create New Thread</div><br></br>
				<div style="margin-left:auto;margin-right:auto">
				<form type="" method="post">
					<input type="text" style="color:white" disabled value="<?php echo $charData[$new_thread_cid]['name']; ?>" style="width: 100px;">
					<input name="create_thread_cid" type="hidden" value="<?php echo $new_thread_cid; ?>">
					<input name="create_thread_category" type="hidden" value="<?php echo $new_thread_category; ?>">
					<input name="create_thread_title" type="text" placeholder="Thread title" style="width: 440px;font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e"><br><br>
					<textarea name="create_thread_text" style="width: 610px; height: 300px;font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;" placeholder="Thread text"></textarea><br>
					<center><button type="submit" value="Create Thread" class="btn yellow flex-cc header__info-button">Create Thread</button></center>
				</form></div></div></div>
				<?php
			} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p><b><font color="red">Permission to create thread denied.</font></b></p></div></div>';
		}
	} else

	/////////////////////
	// When category is specified
	if ($getCat !== false) {
		$getCat = (int)$getCat;

		// Fetch category rules
		$category = mysql_select_single("SELECT `name`, `access`, `closed`, `hidden`, `guild_id` FROM `znote_forum` WHERE `id`='$getCat' AND `access`<='$yourAccess' LIMIT 1;");

		if ($category !== false && $category['guild_id'] > 0 && !$admin) {
			$access = false;
			foreach($charData as $char) if ($category['guild_id'] == $char['guild']) $access = true;
			if ($access !== true) $category = false;
		}

		if ($category !== false) {
			// TODO : Verify guild access
			//foreach($charData)
			echo "<h1><a href='forum.php'>Forum</a> Board: ". $category['name'] ."</h1>";

			// Threads
			//  - id - forum_id - player_id - player_name - title - text - created - updated - sticky - hidden - closed
			$threads = mysql_select_multi("SELECT `id`, `player_name`, `title`, `sticky`, `closed` FROM `znote_forum_threads` WHERE `forum_id`='$getCat' ORDER BY `sticky` DESC, `updated` DESC;");

			///// HTML \\\\\
			if ($threads !== false) {
				?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
				<table class="znoteTable table table-bordered table-striped table-hover" id="forumThreadTable">
					<tr class="yellow">
						<th width="80%"><div class="donate-page__bonuses-title flex-cc">Title</div></th>
						<th width="20%"><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px">By</div></th>
					</tr>
					<?php
					foreach($threads as $thread) {
						$access = true;
						if ($category['hidden'] == 1) {
							if (!$admin) $access = false;
							$access = PlayerHaveAccess($yourChars, $thread['player_name']);
							if ($yourAccess > 3) $access = true;
						}

						if ($access) {
							?>
							<tr class="special">
								<?php
								$url = url("forum.php?forum=". $category['name'] ."&cat=". $getCat ."&thread=". $thread['id']);
								echo '<td onclick="javascript:window.location.href=\'' . $url . '\'"><div class="donate-page__bonuses-title flex-cc">';
								?>
								<!--<td>-->
									<?php
									if ($thread['sticky'] == 1) echo $config['forum']['sticky'],' ';
									if ($thread['closed'] == 1) echo $config['forum']['closed'],' ';
									echo $thread['title'];
									?>
								</div></td>
								<?php
								$url = url("characterprofile.php?name=". $thread['player_name']);
								echo '<td onclick="javascript:window.location.href=\'' . $url . '\'"><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px">';
								?>
								<!--<td>-->
									<?php
									echo $thread['player_name'];
									?>
								</div></td>
							</tr>
							<?php
						}
					}
					?>
				</table></div></div>
				<?php
			} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Board is empty, no threads exist yet.</div></div>';

			///////////
			// Create thread button
			if ($charCount > 0) {
				if ($category['closed'] == 0  || $admin) {
					?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
					<form action="" method="post">
						<input type="hidden" value="<?php echo $getCat; ?>" name="new_thread_category">
						<select name="new_thread_cid" multiple="multiple" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;width:200px">
							<?php
							foreach($yourChars as $char) {
								echo "<option value='". $char['id'] ."'>". $char['name'] ."</option>";
							}
							?>
						</select>
						<button type="submit" value="Create new thread" class="btn yellow flex-cc header__info-button">Create new thread</button>
					</form></div></div>
					<?php
				} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><p>This board is closed.</p></div></div>';
			} else echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><p>You must have a character on your account that is level ". $config['forum']['level'] ."+ to create new threads.</p></div></div>";
		} else echo "<p><font color='red'>Your permission to access this board has been denied.<br>If you are trying to access a Guild Board, you need level: ". $config['forum']['level'] ."+</font></p></div></div>";

	}
} else {

	//////////////////////
	// No category specified, show list of available categories
	if (!$admin) $categories = mysql_select_multi(
		"SELECT `id`, `name`, `access`, `closed`, `hidden`, `guild_id` FROM `znote_forum` WHERE `access`<='$yourAccess' ORDER BY `name`;");
		else $categories = mysql_select_multi("SELECT `id`, `name`, `access`, `closed`, `hidden`, `guild_id` FROM `znote_forum` ORDER BY `name`;");

	$guildboard = false;
	?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div style="margin-left:auto;margin-right:auto">
	<table class="znoteTable table table-striped table-hover" id="forumCategoryTable">
		<tr class="yellow">
			<th>Forum Boards</th>
			<?php
			$guild = false;
			foreach($charData as $char) {
				if ($char['guild'] > 0) $guild = true;
			}

			if ($admin || $guild) {
				if (!isset($guilds))  {
					$guilds = mysql_select_multi("SELECT `id`, `name` FROM `guilds` ORDER BY `name`;");
					$guilds[] = array('id' => '0', 'name' => 'No guild');
				}
				$guildName = array();
				foreach($guilds as $guild) {
					$guildName[$guild['id']] = $guild['name'];
				}
				if ($admin) {
					?>
					<th>Edit</th>
					<th>Delete</th>
					<?php
				}
			}
			?>
		</tr>
		<?php
		if ($categories !== false) {
			foreach ($categories as $category) {
				$access = true;
				if ($category['guild_id'] > 0) {
					$guildboard[] = $category;
					$access = false;
				}

				/*
				if ($guild) {
					foreach($charData as $char) {
						if ($category['guild_id'] == $char['guild']) $access = true;
					}
				}
				*/
				if ($access) {
					$url = url("forum.php?cat=". $category['id']);
					echo '<tr class="special">';
					echo '<td style="font-size:18px;color:white;padding-top:5px" onclick="javascript:window.location.href=\'' . $url . '\'">';
					if ($category['closed'] == 1) echo $config['forum']['closed'],' ';
					if ($category['hidden'] == 1) echo $config['forum']['hidden'],' ';
					if ($category['guild_id'] > 0) {
						echo "[". $guildName[$category['guild_id']] ."] ";
					}
					echo $category['name'] ."</td>";

					// Admin columns
					if ($admin) {
						?>
						<td style="margin-left: 20px; padding: 5px; width: 100px;">
							<form action="" method="post">
								<input type="hidden" name="admin_category_id" value="<?php echo $category['id']; ?>">
								<div style="padding-top:5px">
								<input type="submit" name="admin_category_edit" value="Edit" style="margin: 0px; padding: 0px; width: 100px; height: 22px;" class="btn yellow flex-cc header__info-button"></div>
							</form>
						</td>
						<td style="margin: 0px; padding: 0px; width: 100px;">
							<form action="" method="post">
								<input type="hidden" name="admin_category_id" value="<?php echo $category['id']; ?>">
								<div style="padding-top:5px">
								<input type="submit" name="admin_category_delete" value="Delete" style="margin: 0px; padding: 0px; width: 100px; height: 22px;" class="btn yellow flex-cc header__info-button"></div>
							</form>
						</td>
						<?php
					}
					echo '</tr>';
				}
			}
		}
		?>
	</table></div></div></div>
	<?php
	if ($guildboard !== false && $guild || $guildboard !== false && $admin) {
		//
		?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<div style="margin-left:auto;margin-right:auto">
		<table class="table table-striped table-hover znoteTable" id="forumCategoryTable">
			<tr class="yellow">
				<th>Guild Boards</th>
				<?php
				foreach($charData as $char) {
					if ($char['guild'] > 0) $guild = true;
				}

				if ($admin || $guild) {
					if (!isset($guilds))  {
						$guilds = mysql_select_multi("SELECT `id`, `name` FROM `guilds` ORDER BY `name`;");
						$guilds[] = array('id' => '0', 'name' => 'No guild');
					}
					$guildName = array();
					foreach($guilds as $guild) {
						$guildName[$guild['id']] = $guild['name'];
					}
					if ($admin) {
						?>
						<th width="100">Edit</th>
						<th width="100">Delete</th>
						<?php
					}
				}
				?>
			</tr>
			<?php
			$count = 0;
			foreach ($guildboard as $board) {
				$access = false;
				foreach($charData as $char) {
					if ($board['guild_id'] == $char['guild']) {
						$access = true;
						$count++;
					}
				}
				if ($access || $admin) {
					$url = url("forum.php?cat=". $board['id']);
					echo '<tr class="special">';
					echo '<td onclick="javascript:window.location.href=\'' . $url . '\'">';
					if ($board['closed'] == 1) echo $config['forum']['closed'],' ';
					if ($board['hidden'] == 1) echo $config['forum']['hidden'],' ';
					if ($board['guild_id'] > 0) {
						echo "[". $guildName[$board['guild_id']] ."] ";
					}
					echo $board['name'] ."</td>";

					// Admin columns
					if ($admin) {
						?>
						<td style="margin: 0px; padding: 0px; width: 100px;">
							<form action="" method="post">
								<input type="hidden" name="admin_category_id" value="<?php echo $board['id']; ?>" >
								<input type="submit" name="admin_category_edit" value="Edit" style="margin: 0px; padding: 0px; width: 50px; height: 22px;" class="btn yellow flex-cc header__info-button">
							</form>
						</td>
						<td style="margin: 0px; padding: 0px; width: 100px;">
							<form action="" method="post">
								<input type="hidden" name="admin_category_id" value="<?php echo $board['id']; ?>" >
								<input type="submit" name="admin_category_delete" value="Delete" style="margin: 0px; padding: 0px; width: 75px; height: 22px;" class="btn yellow flex-cc header__info-button">
							</form>
						</td>
						<?php
					}
					echo '</tr>';
				}
			}
			if ($count == 0 && !$admin) echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><tr><td>You don\'t have access to any guildboards.</td></tr></div></div>';
			?>
		</table></div></div></div>
		<?php
	}
	if ($admin) {
		?>
		<div class="global-desc__content" style="width:100%"><div class="desc-changelog__block flex-ss" style="">
		<br></br><br></br>
		<div class="donate-page__title" style="color:white">Create board:</div>
		<div style="margin-left:auto;margin-right:auto">
		<center>
		<form action="" method="post">
			<input type="text" name="admin_board_create_name" placeholder="Board name" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;"><br><br>

			Required access: <select name="admin_board_create_access" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<?php
				foreach($config['ingame_positions'] as $access => $name) {
					echo "<option value='$access'>$name</option>";
				}
				?>
			</select><br><br>

			Board closed: <select name="admin_board_create_closed" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select><br>

			Board hidden: <select name="admin_board_create_hidden" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select><br><br>

			Guild board: <select name="admin_board_create_guild_id" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<?php
				foreach($guilds as $guild) {
					if ($guild['id'] == 0) echo "<option value='". $guild['id'] ."' selected>". $guild['name'] ."</option>";
					else echo "<option value='". $guild['id'] ."'>". $guild['name'] ."</option>";
				}
				?>
			</select><br><br>

			<input type="submit" value="Create Board" class="btn yellow flex-cc header__info-button">
		</form></center></div></div></div>
		<?php
	}

}


include 'layout/overall/footer_login.php'; ?>
