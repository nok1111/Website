<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<div class="donate-page__title" style="color:white">Login</div><br></br>
	<div style="margin-left:auto;margin-right:auto">
	<br></br>
	<form class="loginForm" action="login.php" method="post">
	<div class="global-form__line">
	<div class="donate-page__title" style="color:white;font-size:16px">Userame:</div> <input type="text" name="username" id="login_username" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;text-align:center">
			</div>
			<div class="global-form__line">
			<div class="donate-page__title" style="color:white;font-size:16px">Password:</div> <input type="password" name="password" id="login_password" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;text-align:center">
			</div>
			<?php if ($config['twoFactorAuthenticator']): ?>
				<div class="global-form__line">
				<div class="donate-page__title" style="color:white;font-size:16px">Token:</div> <input type="password" name="authcode" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				</div>
			<?php endif; ?>
			<div class="global-form__line">
				
				<button type="submit" value="Log in" class="submitButton btn yellow flex-cc header__info-button">Log in </button>
			</div>
			<?php
				/* Form file */
				Token::create();
			?>
			
			<center>
			<div class="hts__block-files-title"><a href="register.php">New account</a></div>
			<br>
			<div class="hts__block-files-title">Lost <a href="recovery.php?mode=username">username</a> or <a href="recovery.php?mode=password">password</a>?</div>
			</center>
		</form>
</div>
			</div></div>

<?php
include 'layout/overall/footer_login.php'; ?>
