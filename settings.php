<?php
require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';
require_once('config.countries.php');

if (empty($_POST) === false) {
	// $_POST['']
	/* Token used for cross site scripting security */
	if (!Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}
	$required_fields = array('new_email', 'new_flag');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">You need to fill in all fields.</div></div>';
			break 1;
		}
	}

	if (empty($errors) === true) {
		if (filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">A valid email address is required.</div></div>';
		} else if (user_email_exist($_POST['new_email']) === true && $user_data['email'] !== $_POST['new_email']) {
			$errors[] = '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">That email address is already in use.</div></div>';
		}
	}
}
?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<div class="donate-page__title" style="color:white">Settings</div>

<?php
if (isset($_GET['success']) === true && empty($_GET['success']) === true) {
	echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your settings have been updated.</div></div>';
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		$update_data = array(
			'email' => $_POST['new_email']
		);

		$update_znote_data = array(
			'flag' => getValue($_POST['new_flag']),
			'active_email' => '0'
		);

		// If he had previously verified his email address, remove the previously aquired bonus points
		if ($user_znote_data['active_email'] > 0) {
			$update_znote_data['points'] = $user_znote_data['points'] - $config['mailserver']['verify_email_points'];
		}

		user_update_account($update_data);
		user_update_znote_account($update_znote_data);
		header('Location: settings.php?success');
		exit();

	} else if (empty($errors) === false) {
		echo output_errors($errors);
	}
	?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<div style="margin-left:auto;margin-right:auto;">
	<form action="" method="post">
		<ul>
		<div class='donate-page__bonuses-title flex-cc'>
				Email:<br></br></div>
				<input type="text" name="new_email" value="<?php echo $user_data['email']; ?>" style="width:403px;font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">

			<div class='donate-page__bonuses-title flex-cc'>
				Country:<br></br></div>
				<select name="new_flag" id="flag_select" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
					<option value="">(Please choose)</option>
					<?php
					foreach(array('pl', 'se', 'br', 'us', 'gb', ) as $c)
						echo '<option value="' . $c . '">' . $config['countries'][$c] . '</option>';

						echo '<option value="">----------</option>';
						foreach($config['countries'] as $code => $c)
							echo '<option value="' . $code . '"' . (isset($user_znote_data['flag']) && $user_znote_data['flag'] == $code ? ' selected' : '') . '>' . $c . '</option>';
					?>
				</select>
</div>
			<?php
				/* Form file */
				Token::create();
			?>
			<div class='donate-page__bonuses-title flex-cc'>
				<button type="submit" class="btn yellow flex-cc header__info-button" value="Update settings">Update Settings</button>
</div>
		</ul>
	</form></div></div></div>
<?php
}
include 'layout/overall/footer_login.php';
?>
