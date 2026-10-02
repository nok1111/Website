<?php
require_once 'engine/init.php';
logged_in_redirect();
include 'layout/overall/header.php';
require_once('config.countries.php');

if (empty($_POST) === false) {
	// $_POST['']
	$required_fields = array('username', 'password', 'password_again', 'email', 'selected');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">You need to fill in all fields.</div></div>';
			break 1;
		}
	}

	// check errors (= user exist, pass long enough
	if (empty($errors) === true) {
		/* Token used for cross site scripting security */
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}

		if ($config['use_captcha']) {
			if(!verifyGoogleReCaptcha($_POST['g-recaptcha-response'])) {
				$errors[] = "Please confirm that you're not a robot.";
			}
		}

		if (user_exist($_POST['username']) === true) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Sorry, that username already exist.</div></div>';
		}

		// Don't allow "default admin names in config.php" access to register.
		$isNoob = in_array(strtolower($_POST['username']), $config['page_admin_access']) ? true : false;
		if ($isNoob) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">This account name is blocked for registration.</div></div>';
		}
		if ($config['client'] >= 830) {
			if (preg_match("/^[a-zA-Z0-9]+$/", $_POST['username']) == false) {
				$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your account name can only contain characters a-z, A-Z and 0-9.</div></div>';
			}
		} else {
			if (preg_match("/^[0-9]+$/", $_POST['username']) == false) {
				$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your account can only contain numbers 0-9.</div></div>';
			}
			if ((int)$_POST['username'] < 100000 || (int)$_POST['username'] > 999999999) {
				$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your account number must be a value between 6-8 numbers long.</div></div>';
			}
		}
		// name restriction
		$resname = explode(" ", $_POST['username']);
		foreach($resname as $res) {
			if(in_array(strtolower($res), $config['invalidNameTags'])) {
				$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your username contains a restricted word.</div></div>';
			}
			else if(strlen($res) == 1) {
				$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Too short words in your name.</div></div>';
			}
		}
		if (strlen($_POST['username']) > 32) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your account name must be less than 33 characters.</div></div>';
		}
		// end name restriction
		if (strlen($_POST['password']) < 6) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your password must be at least 6 characters.</div></div>';
		}
		if (strlen($_POST['password']) > 29) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your password must be less than 30 characters.</div></div>';
		}
		if ($_POST['password'] !== $_POST['password_again']) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your passwords do not match.</div></div>';
		}
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">A valid email address is required.</div></div>';
		}
		if (user_email_exist($_POST['email']) === true) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">That email address is already in use.</div></div>';
		}
		if ($_POST['selected'] != 1) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">You are only allowed to have an account if you accept the rules.</div></div>';
		}
		if ($config['validate_IP'] === true) {
			if (validate_ip(getIP()) === false) {
				$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Failed to recognize your IP address. (Not a valid IPv4 address).</div></div>';
			}
		}
		if (strlen($_POST['flag']) < 1) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Please choose country.</div></div>';
		}
	}
}

?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<div class='donate-page__title' style='color:white'>Register Account</div></div></div>
<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	if ($config['mailserver']['register']) {
		?>
		<h1>Email authentication required</h1>
		<p>We have sent you an email with an activation link to your submitted email address.</p>
		<p>If you can't find the email within 5 minutes, check your <strong>junk/trash inbox (spam filter)</strong> as it may be mislocated there.</p>
		<?php
	} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style=""><font color="lime">Congratulations</font>! Your account has been created. You may now&thinsp; <a href="login_1.php">&thinsp;login&thinsp;</a> to create a character.</div></div><br></br><br></br><br></br><br></br><br></br><br></br>';
} elseif (isset($_GET['authenticate']) && empty($_GET['authenticate'])) {
	// Authenticate user, fetch user id and activation key
	$auid = (isset($_GET['u']) && (int)$_GET['u'] > 0) ? (int)$_GET['u'] : false;
	$akey = (isset($_GET['k']) && (int)$_GET['k'] > 0) ? (int)$_GET['k'] : false;
	// Find a match
	$user = mysql_select_single("SELECT `id`, `active`, `active_email` FROM `znote_accounts` WHERE `account_id`='$auid' AND `activekey`='$akey' LIMIT 1;");
	if ($user !== false) {
		$user = (int) $user['id'];
		$active = (int) $user['active'];
		$active_email = (int) $user['active_email'];
		// Enable the account to login
		if ($active == 0 || $active_email == 0) {
			mysql_update("UPDATE `znote_accounts` SET `active`='1', `active_email`='1' WHERE `id`= $user LIMIT 1;");
		}
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style=""><h1>Congratulations!</h1> <p>Your account has been created. You may now login to create a character.</p></div></div>';
	} else {
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style=""><h1>Authentication failed</h1> <p>Either the activation link is wrong, or your account is already activated.</p></div></div>';
	}
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(1);
		}

		//Register
		$register_data = array(
			'name'		=>	$_POST['username'],
			'password'	=>	$_POST['password'],
			'email'		=>	$_POST['email'],
			'created'	=>	time(),
			'ip'		=>	getIPLong(),
			'flag'		=> 	$_POST['flag']
		);

		user_create_account($register_data, $config['mailserver']);
		if (!$config['mailserver']['debug']) header('Location: register.php?success');
		exit();
		//End register

	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<center>
	<form action="" method="post">
		<ul>
			
		<div class="donate-page__bonuses-title flex-cc">Account Name:<br></div>
				<input type="text" name="username" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;height:35px">
			

			<div class="donate-page__bonuses-title flex-cc">Password:<br></div>
				<input type="password" name="password" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;height:35px">

			
<div class="donate-page__bonuses-title flex-cc">Password again:<br></div>
				<input type="password" name="password_again" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;height:35px">

			
<div class="donate-page__bonuses-title flex-cc">Email:<br></div>
				<input type="text" name="email" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;height:35px">

			
<div class="donate-page__bonuses-title flex-cc">Country:<br></div>
				<select name="flag" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;height:35px">
					<option value="">(Please choose)</option>
					<?php
					foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $c)
						echo '<option value="' . $c . '">' . $config['countries'][$c] . '</option>';

						echo '<option value="">----------</option>';
						foreach($config['countries'] as $code => $c)
							echo '<option value="' . $code . '">' . $c . '</option>';
					?>
				</select>

			
			<?php
			if ($config['use_captcha']) {
				?>
				<div class="donate-page__bonuses-title flex-cc">
					 <div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div>
			</div>
				<?php
			}
			?>

<div class="donate-page__bonuses-title flex-cc"><div class="donate-page__title" style="color:white">Server Rules<br></br></div>
				<p>The golden rule: Have fun.</p>
				<p>If you get pwn3d, don't hate the game.</p>
				<p>No <a href='https://en.wikipedia.org/wiki/Cheating_in_video_games' target="_blank">cheating</a> allowed.</p>
				<p>No <a href='https://en.wikipedia.org/wiki/Video_game_bot' target="_blank">botting</a> allowed.</p>
				<p>The staff can delete, ban, do whatever they want with your account and your <br>
					submitted information. (Including exposing and logging your IP).</p>
		</div>
		</div></div>
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<div class="donate-page__bonuses-title flex-cc">Do you agree to follow the server rules?</div>
		<div class="desc-changelog__block flex-ss" style="">
		<div style="margin-left:auto;margin-right:auto">
				<select name="selected" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				  <option value="0">Umh...</option>
				  <option value="1">Yes.</option>
				  <option value="2">No.</option>
				</select>
		</div></div>
			<?php
				/* Form file */
				Token::create();
			?>
			<div class="donate-page__bonuses-title flex-cc">
				<button type="submit" value="Create Account" class="btn yellow flex-cc header__info-button">Create Account</button>
		</div>
		</ul></div>
	</form></center></div></div>
<?php
}
include 'layout/overall/footer_login.php';
?>
