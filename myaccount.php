<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';
#region CANCEL CHARACTER DELETE
$undelete_id = @$_GET['cancel_delete_id'];
if($undelete_id) {
	$undelete_id = (int)$undelete_id;
	$undelete_q1 = mysql_select_single("
		SELECT 
			`character_name` 
		FROM `znote_deleted_characters` 
		WHERE `done` = 0 
		AND `id` = {$undelete_id} 
		AND `original_account_id` = {$session_user_id} 
		AND NOW() < `time`
	");
	if($undelete_q1) {
		mysql_delete('DELETE FROM `znote_deleted_characters` WHERE `id` = ' . $undelete_id);
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Pending delete of ' . $undelete_q1['character_name'] . ' has been successfully canceled.<br/></div></div>';
	}
}
#endregion

// Variable used to check if main page should be rendered after handling POST (Change comment page)
$render_page = true;

// Handle GET (verify email)
if (isset($_GET['authenticate']) && $config['mailserver']['myaccount_verify_email']):
	// If we need to process email verification
	if (isset($_GET['u']) && isset($_GET['k'])) {
		// Authenticate user, fetch user id and activation key
		$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
		$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
		if ($auid !== false && $akey !== false) {
			// Find a match
			$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='{$auid}' AND `activekey`='{$akey}' LIMIT 1;");
			if ($user !== false) {
				$user = (int) $user['id'];
				$active = (int) $user['active'];
				$active_email = (int) $user['active_email'];
				$verify_points = ($active_email == 0 && $config['mailserver']['verify_email_points'] > 0)
					? ", `points` = `points` + {$config['mailserver']['verify_email_points']}"
					: '';
				// Enable the account to login
				if ($active == 0 || $active_email == 0) {
					$new_activeKey = rand(100000000, 999999999);
					mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1', `activekey`='{$new_activeKey}' {$verify_points} WHERE `id`= {$user} LIMIT 1;");
				}
				echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><div class="donate-page__title" style="color:white">Congratulations!</div> <p>Your email has been verified.</p>';
				if ($verify_points !== '') echo "<p>As thanks for having a verified email, you have received <a href='/shop.php'>{$config['mailserver']['verify_email_points']} shop points</a>!</p></div></div>";
				$user_znote_data['active_email'] = 1;
				// Todo: Bonus points as thanks for verifying email
			} else {
				echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Authentication failed <p>Either the activation link is wrong, or your account is already activated.</p></div></div>';
			}
		} else {
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Authentication failed <p>Either the activation link is wrong, or your account is already activated.</p></div></div>';
		}
	} else { // We need to send email verification
		$verify_account_id = (int)$session_user_id;
		$user = mysql_select_single("SELECT `id`, `activekey`, `active_email` FROM `znote_accounts` WHERE `account_id`='{$verify_account_id}' LIMIT 1;");
		if ($user !== false) {
			$thisurl = config('site_url') . "/myaccount.php";
			$thisurl .= "?authenticate&u=".$verify_account_id."&k=".$user['activekey'];

			$mailer = new Mail($config['mailserver']);

			$title = "Please authenticate your email at {$_SERVER['HTTP_HOST']}.";

			$body = "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'>Please click on the following link to authenticate your account:</div></div>";
			$body .= "<p><a href='{$thisurl}'>{$thisurl}</a></p>";
			$body .= "<p>Thank you for verifying your email and enjoy your stay at {$config['mailserver']['fromName']}.</p>";
			$body .= "<hr><p>I am an automatic no-reply e-mail. Any emails sent back to me will be ignored.</p></div></div>";

			$user_name = ($config['ServerEngine'] !== 'OTHIRE') ? $user_data['name'] : $user_data['id'];
			//echo "<h1>" . $title . "<h1>" . $body;
			$mailer->sendMail($user_data['email'], $title, $body, $user_name);
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
			<h1>Email authentication sent</h1>
			<p>We have sent you an email with a verification link to your email address: <strong><?php echo $user_data['email']; ?></strong></p>
			<p>If you can't find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be misplaced there.</p></div></div>
			<?php
		} else {
			echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Authentication failed <p>Failed to verify user when trying to send a verification email.</p></div></div>';
		}
	}
endif;

// Handle POST
if (!empty($_POST['selected_character'])) {
	if (!empty($_POST['action'])) {
		// Validate token
		if (!Token::isValid($_POST['token'])) {
			exit();
		}
		// Sanitize values
		$action = getValue($_POST['action']);
		$char_name = getValue($_POST['selected_character']);

		// Handle actions
		switch($action) {
			// Change character comment PAGE2 (Success).
			case 'update_comment':
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					user_update_comment(user_character_id($char_name), getValue($_POST['comment']));
					echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Successfully updated comment.</div></div>';
				}
				break;
			// end

			// Hide character
			case 'toggle_hide':
				$hide = (user_character_hide($char_name) == 1 ? 0 : 1);
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					user_character_set_hide(user_character_id($char_name), $hide);
				}
				break;
			// end

			// DELETE character
			case 'delete_character':
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					$charid = user_character_id($char_name);
					if ($charid !== false) {
						if (!user_is_online_10($charid)) {
							if (guild_leader_gid($charid) === false) user_delete_character_soft($charid);
							else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Character is leader of a guild, you must disband the guild or change leadership before deleting character.</div></div>';
						} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Character must be offline first.</div></div>';
					}
				}
				break;
			// end

			// CHANGE character name
			case 'change_name':
				$oldname = $char_name;
				$newname = isset($_POST['newName']) ? getValue($_POST['newName']) : '';

				$player = false;
				$player = mysql_select_single("SELECT `id`, `account_id` FROM `players` WHERE `name` = '$oldname'");
				$player['online'] = (user_is_online_10($player['id'])) ? 1 : 0;

				// Check if user is online
				if ($player['online'] == 1) {
					$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Character must be offline first.</div></div></div>';
				}

				// Check if player has bough ticket
				$accountId = $player['account_id'];
				$order = mysql_select_single("SELECT `id`, `account_id` FROM `znote_shop_orders` WHERE `type`='4' AND `account_id` = '$accountId' LIMIT 1;");
				if ($order === false) {
					$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Did not find any name change tickets, buy them in our <a href="shop.php">shop!</a></div></div>';
				}

				// Check if player and account matches
				if ($session_user_id != $accountId || $session_user_id != $order['account_id']) {
					if (empty($errors)) {
						$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Failed to sync your account. </div></div>';
					}
				}

				$newname = validate_name($newname);
				if ($newname === false) {
					$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Your name can not contain more than 2 words.</div></div>';
				} else {
					if (empty($newname)) {
						$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Please enter a name!</div></div>';
					} else if (user_character_exist($newname) !== false) {
						$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Sorry, that character name already exist.</div></div>';
					} else if (!preg_match("/^[a-zA-Z_ ]+$/", $newname)) {
						$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Your name may only contain a-z, A-Z and spaces.</div></div>';
					} else if (strlen($newname) < $config['minL'] || strlen($newname) > $config['maxL']) {
						$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.</div></div>';
					} else if (!ctype_upper($newname[0])) {
						$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">The first letter of a name has to be a capital letter!</div></div>';
					}

					// name restriction
					$resname = explode(" ", $_POST['newName']);
					foreach($resname as $res) {
						if(in_array(strtolower($res), $config['invalidNameTags'])) {
							$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Your username contains a restricted word.</div></div>';
						} else if(strlen($res) == 1) {
							$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Too short words in your name.</div></div>';
						}
					}
				}

				if (!empty($newname) && empty($errors)) {
					echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">You have successfully changed your character name to ' . $newname . '.</div></div>';
					mysql_update("UPDATE `players` SET `name`='$newname' WHERE `id`='".$player['id']."' LIMIT 1;");
					mysql_delete("DELETE FROM `znote_shop_orders` WHERE `id`='".$order['id']."' LIMIT 1;");

				} else if (!empty($errors)) {
					echo '<font color="red"><b>';
					echo output_errors($errors);
					echo '</b></font>';
				}

				break;
			// end

			// Change character sex
			case 'change_gender':
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					$char_id = (int)user_character_id($char_name);
					$account_id = user_character_account_id($char_name);

					$chr_data['online'] = user_is_online_10($char_id) ? 1 : 0;
					if ($chr_data['online'] != 1) {
						// Verify that we are not messing around with data
						if ($account_id != $user_data['id']) die("wtf? Something went wrong, try relogging.");

						// Fetch character tickets
						$tickets = shop_account_gender_tickets($account_id);
						if ($tickets !== false || $config['free_sex_change'] == true) {
							// They are allowed to change gender
							$last = false;
							$infinite = false;
							$tks = 0;
							// Do we have any infinite tickets?
							foreach ($tickets as $ticket) {
								if ($ticket['count'] == 0) $infinite = true;
								else if ($ticket > 0 && $infinite === false) $tks += (int)$ticket['count'];
							}
							if ($infinite === true) $tks = 0;
							$dbid = (int)$tickets[0]['id'];
							// If they dont have unlimited tickets, remove a count from their ticket.
							if ($tickets[0]['count'] > 1) { // Decrease count
								$tks--;
								$tkr = ((int)$tickets[0]['count'] - 1);
								shop_update_row_count($dbid, $tkr);
							} else if ($tickets[0]['count'] == 1) { // Delete record
								shop_delete_row_order($dbid);
								$tks--;
							}

							// Change character gender:
							//
							user_character_change_gender($char_name);
							echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">You have successfully changed gender on character '. $char_name .'.';
							if ($tks > 0) echo '<br>You have '. $tks .' gender change tickets left.</div></div>';
							else if ($infinite !== true) echo '<br>You are out of tickets.</div></div>';
						} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">You don\'t have any character gender tickets, buy them in the <a href="shop.php">SHOP</a>!</div></div>';
					} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">Your character must be offline.</div></div>';
				}
				break;
			// end

			// Change character comment PAGE1:
			case 'change_comment':
				$render_page = false; // Regular "myaccount" page should not render
				if ((int)user_character_account_id($char_name) === $session_user_id) {
					$comment_data = user_znote_character_data(user_character_id($char_name), 'comment');
					?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
					<!-- Changing comment MARKUP -->
					<h1>Change comment on:</h1>
					<form action="" method="post">
						<ul>
							<li>
								<input name="action" type="hidden" value="update_comment">
								<input name ="selected_character" type="text" value="<?php echo $char_name; ?>" readonly="readonly">
							</li>
							<li>
								<font class="profile_font" name="profile_font_comment">Comment:</font> <br>
								<textarea name="comment" cols="70" rows="10"><?php echo $comment_data['comment']; ?></textarea>
							</li>
							<?php
								/* Form file */
								Token::create();
							?>
							<li><input type="submit" value="Update Comment"></li>
						</ul>
					</form></div></div>
					<?php
				}
				break;
			//end
		}
	}
}

if ($render_page) {
	$char_count = user_character_list_count($session_user_id);
	$pending_delete = user_pending_deletes($session_user_id);
	if ($pending_delete) {
		foreach($pending_delete as $delete) {
			if(new DateTime($delete['time']) > new DateTime())
				echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><b>CAUTION!</b> Your character with name <b>' . $delete['character_name'] . ' will be deleted on ' . $delete['time'] . '</b>. <a href="myaccount.php?cancel_delete_id=' . $delete['id'] . '">Cancel this operation.</a><br/></div></div>';
			else {
				user_delete_character(user_character_id($delete['character_name']));
				mysql_update('UPDATE `znote_deleted_characters` SET `done` = 1 WHERE `id` = '. $delete['id']. '');
				echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style=""><b>Character ' . $delete['character_name'] . ' has been deleted</b>. This operation was requested by owner of this account.</div></div>';
				$char_count--;
			}
		}
	}

	?>
	<?php
			// If admin
			if (is_admin($user_data)) {
			?>
			
			<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px;border: 1px solid lime"><div class="desc-changelog__block flex-ss" style="">
			<div class="donate-page__title" style="color:white"> Admin Panel </div>
			<div style="margin-left:auto;margin-right:auto">
			<p><i>Only Admins can view this panel.</i></p>
			<br></br>
			<select class="form-control " onchange="location = this.options[this.selectedIndex].value;" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;text-align:center;width:200px;height:25px;">
								<option value="none" selected>Select action</option>
								<option value="admin.php">Admin Panel</option>
								<option value='changelog.php'>Changelogs</option>
								<option value="admin_news.php">Admin Create News</option>
								<option value="admin_gallery.php">Admin Gallery</option>
								<option value="admin_skills.php" >Admin Skills</option>
								<option value="admin_reports.php" >Admin Reports</option>
								<option value="helpdesk.php" >Admin Tickets</option>
								<option value="admin_shop.php" >Admin Shop</option>
								<option value="admin_auction.php" >Admin Character Auction</option>
								<option value="forum.php?cat=4" >Forum Feedback</option>
							</select></div>
							
</div></div>
<?php
			}
			// end if admin
			?>
			
	<div id="myaccount">
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px;border:1px solid orange"><div class="desc-changelog__block flex-ss">
		
	<div class="donate-page__title" style="color:white"> My Account </div></div>

		<div class='desc-changelog__block flex-ss' style='font-size:18px'>
		<div style="margin-left:auto;margin-right:auto">
		<p>Welcome to your Account page, <font color="orange"><?php echo $user_data['name']; ?></font><br>
			<?php 
			// Todo: If premdays is less than 2, inform how many hours are left.
			if ($user_data['premdays'] != 0) echo "You have  &thinsp;<font color='lime'>{$user_data['premdays']}</font>&thinsp; remaining Premium Account days.</div></div>"; 
			else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;You are Free Account.</div></div>';

			if ($config['mailserver']['myaccount_verify_email']):
				?><br>Email: <?php echo $user_data['email'];
				if ($user_znote_data['active_email'] == 1) {
					?> (Verified).<?php
				} else {
					?><div class="desc-changelog__block flex-ss"><br><strong>Your email is not verified! <a href="?authenticate">Please verify it</a>.</strong></div><?php
				}
			endif; ?>
		</p>
		<?php
		if ($config['twoFactorAuthenticator']) {
			$query = mysql_select_single("SELECT `secret` FROM `accounts` WHERE `id`='".(int)$session_user_id."' LIMIT 1;");
			$status = ($query['secret'] === NULL) ? false : true;
			?><p>Account security with Two-factor Authentication: <a href="twofa.php"><?php echo ($status) ? 'Enabled' : 'Disabled'; ?></a></p><?php
		}
		?><div class="desc-changelog__block flex-ss">
		<div class="donate-page__title" style="color:white">Characters: <?php echo $char_count; ?> characters.</div></div>
		<?php
		// Echo character list!
		$char_array = user_character_list($user_data['id']);
		// Design and present the list
		if ($char_array) {
			?>
			<div class="desc-changelog__block flex-ss" style="text-align:center">
			<table id="myaccountTable" class="table table-striped table-hover">
				<tr class="yellow">
					<th><div class="donate-page__bonuses-title flex-cc">NAME</div></th>
					<th><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">LEVEL</div></th>
					<th><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">VOCATION</div></th>
					<th><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">TOWN</div></th>
					<th><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">LAST LOGIN</div></th>
					<th><div class="donate-page__bonuses-title flex-cc" style="padding-left:25px">STATUS</div></th>
					<th><div class="donate-page__bonuses-title flex-cc" style="padding-left:25px">HIDE</div></th>
				</tr>
				<?php
				foreach ($char_array as $value): ?>
					<tr>
						<td><div class="donate-page__bonuses-title flex-cc"><a href="characterprofile.php?name=<?php echo $value['name']; ?>"><?php echo $value['name']; ?></a></div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $value['level']; ?></div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><?php echo $value['vocation']; ?></div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><?php echo $value['town_id']; ?></div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $value['lastlogin']; ?></div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:25px"><?php echo $value['online']; ?></div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:25px"><?php echo hide_char_to_name($value['hide_char']); ?></div></td>
					</tr>
					<?php
				endforeach;
				?>
			</table></div>
			<!-- FORMS TO EDIT CHARACTER-->
			<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss">
			<div style="margin-left:auto;margin-right:auto">
			<form action="" method="post">
				<table class="table">
					<tr>
						<td><center>
							<select id="selected_character" name="selected_character" class="form-control" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
								<?php foreach ($char_array as $character): ?>
									<option value="<?php echo $character['name']; ?>"><?php echo $character['name']; ?></option>
								<?php endforeach; ?>
							</select></center>
						</td>
						<tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><td><center>
							<select id="action" name="action" class="form-control" onChange="changedOption(this)" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
								<option value="none" selected>Select action</option>
								<option value="toggle_hide">Toggle hide</option>
								<option value="delete_character" class="needconfirmation">Delete character</option>
							</select></center>
						</td></tr><tr>
						<td id="submit_form">
							<?php
								/* Form file */
								Token::create();
							?>
							<button id="submit_button" type="submit" class="btn yellow flex-cc header__info-button" onclick="window.location.reload();">Submit</button>

						</td></tr>
					</tr>
				</table>
			</form></div></div></div>
			<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss">
			<div style="margin-left:auto;margin-right:auto">
			<table>
			<td><a href="createcharacter.php"><button type="submit" value="Submit" class="btn yellow flex-cc header__info-button"><b>Create Character</b></button></a></td>
			<td><a href="settings.php"><button type="submit" value="Submit" class="btn yellow flex-cc header__info-button"><b>Settings</b></button></a></td>
			<td><a href="changepassword.php"><button type="submit" value="Submit" class="btn yellow flex-cc header__info-button"><b>Change Password</b></button></a></td>
			<td><a href="logout.php"><button type="submit" value="Submit" class="btn yellow flex-cc header__info-button"><b>Logout</b></button></a></td>
								</table></div>	</div></div>
			<?php
		} else {
			echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><a href='createcharacter.php'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Create Character</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='logout.php'>Logout.</a></div></div>";
		}
		?>
		</div></div>
	</div>
	<script>
		function changedOption(e) {
			// If selection is 'Change name' add a name field in the form
			// Else remove name field if it exists
			if (e.value == 'change_name') {
				var lastCell = document.getElementById('submit_form');
				var x = document.createElement('TD');
				x.id = "new_name";
				x.innerHTML = '<input type="text" name="newName" placeholder="New Name" class="form-control">';
				lastCell.parentNode.insertBefore(x, lastCell);
			} else {
				var child = document.getElementById('new_name');
				if (child) {
					child.parentNode.removeChild(child);
				}
			}
		}
	</script>
	<script src="engine/js/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(){
			$("#submit_button").click(function(e){
				if ($("#action").find(":selected").attr('class') == "needconfirmation") {
					var r = confirm("Do you really want to DELETE character: "+$('#selected_character').find(":selected").text()+"?")
					if (r == false) {
						e.preventDefault();
					}
				}
			});
		});
	</script>
	<?php
}
include 'layout/overall/footer_login.php';
?>
