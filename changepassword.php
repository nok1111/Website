<?php require_once 'engine/init.php';
protect_page();

if (empty($_POST) === false) {
	/* Token used for cross site scripting security */
	if (!Token::isValid($_POST['token'])) {
		$errors[] = 'Token is invalid.';
	}

	$required_fields = array('current_password', 'new_password', 'new_password_again');

	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	$pass_data = user_data($session_user_id, 'password');
	//$pass_data['password'];
	// $_POST['']

	// .3 compatibility
	if ($config['ServerEngine'] == 'TFS_03' && $config['salt'] === true) {
		$salt = user_data($session_user_id, 'salt');
	}
	if (sha1($_POST['current_password']) === $pass_data['password'] || $config['ServerEngine'] == 'TFS_03' && $config['salt'] === true && sha1($salt['salt'].$_POST['current_password']) === $pass_data['password']) {
		if (trim($_POST['new_password']) !== trim($_POST['new_password_again'])) {
			$errors[] = 'Your new passwords do not match.';
		} else if (strlen($_POST['new_password']) < 6) {
			$errors[] = 'Your new passwords must be at least 6 characters.';
		} else if (strlen($_POST['new_password']) > 100) {
			$errors[] = 'Your new passwords must be less than 100 characters.';
		}
	} else {
		$errors[] = 'Your current password is incorrect.';
	}
}

include 'layout/overall/header.php'; ?>

<div class="donate-page__title" style="color:white">Change Password</div><br></br>

<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">Your password has been changed.<br>You will need to login again with the new password.</div></div>';
	session_destroy();
	header("refresh:2;url=index.php");
	exit();
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		//Posted the form without errors
		if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
			user_change_password($session_user_id, $_POST['new_password']);
		} else if ($config['ServerEngine'] == 'TFS_03') {
			user_change_password03($session_user_id, $_POST['new_password']);
		}
		header('Location: changepassword.php?success');
	} else if (empty($errors) === false){
		echo '<font color="red"><b>';
		echo output_errors($errors);
		echo '</b></font>';
	}
	?><center>
		<div class="donate-page__info">
		<div class="donate-page__content" id="donate_form">
		<div class="donate-page__title">Contact Us</div>
		<div class="global-form">
	<form action="" method="post">
	<div class="donate-page__desc">
				Current password:<br>
				<input type="password" name="current_password">
</div>
<div class="donate-page__desc">
				New password:<br>
				<input type="password" name="new_password">
</div>
<div class="donate-page__desc">
				New password again:<br>
				<input type="password" name="new_password_again">
</div>
			<?php
				/* Form file */
				Token::create();
			?>
			<div class="global-form__line">
				<center><button type="submit" value="Change password" class="btn yellow flex-cc header__info-button">Change Password</button></center>
</div>
		</ul>
	</form></div></div></center>
<?php
}
include 'layout/overall/footer_login.php'; ?>
