<?php
require_once 'engine/init.php';
// To direct users here, add: protect_page(); Here before loading header.
include 'layout/overall/header.php';
if (user_logged_in() === true) {
?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<div class='donate-page__title' style='color:white'>STOP!</div>
<p>Ummh... Why are you sniffing around here?</p>
<p>Please <a href="index.php">Go back</a>.</p>
</div></div>
<?php
} else {
?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<h3>Sorry, you need to be logged in to do that!<br></br>
Please <a href="register.php">register</a> or <a href="login_1.php">log in</a>.</h3><br></br>
</div></div>
<?php
}
include 'layout/overall/footer.php'; ?>
