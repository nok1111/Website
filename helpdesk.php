<?php
require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

$view = (isset($_GET['view']) && (int)$_GET['view'] > 0) ? (int)$_GET['view'] : false;
if ($view !== false) {
	if (!empty($_POST['reply_text'])) {

		// Save ticket reply on database
		$query = array(
			'tid'   =>	$view,
			'username'=>	getValue($_POST['username']),
			'message' =>	getValue($_POST['reply_text']),
			'created' =>	time(),
		);
		$fields = '`'. implode('`, `', array_keys($query)) .'`';
		$data = '\''. implode('\', \'', $query) .'\'';
		mysql_insert("INSERT INTO `znote_tickets_replies` ($fields) VALUES ($data)");
		mysql_update("UPDATE `znote_tickets` SET `status`='Player-Reply' WHERE `id`='$view' LIMIT 1;");
	}
	$ticketData = mysql_select_single("SELECT * FROM znote_tickets WHERE id='$view' LIMIT 1;");

	if(!$ticketData || $ticketData['owner'] != $session_user_id) {
		echo 'You can not view this ticket!';
		include 'layout/overall/footer.php';
		die;
	}
	?>
	<div class="donate-page__title" style="color:white">View Ticket #
	<?php
		echo $ticketData['id'];
		if ($ticketData['status'] === 'CLOSED') {
			echo '<span style="color:red">[CLOSED]</SPAN>';
		}
	?></div>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<table class="znoteTable ThreadTable table table-striped">
		<tr class="yellow">
			<th>
				<?php
					echo getClock($ticketData['creation'], true);
				?>
				 - Created by:
				 <?php
				 	echo $ticketData['username'];
				 ?>
			</th>
		</tr>
		<tr>
			<td>
				<p><?php echo nl2br($ticketData['message']); ?></p>
			</td>
		</tr>
	</table></div></div>
	<?php
	$replies = mysql_select_multi("SELECT * FROM znote_tickets_replies WHERE tid='$view' ORDER BY `created`;");
	if ($replies !== false) {
		foreach($replies as $reply) {
			?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
			<table class="znoteTable ThreadTable table table-striped">
				<tr class="yellow">
					<th>
						<?php
							echo getClock($reply['created'], true);
						?>
						 - Posted by:
						 <?php
						 	echo $reply['username'];
						 ?>
					</th>
				</tr>
				<tr>
					<td>
						<p><?php echo nl2br($reply['message']); ?></p>
					</td>
				</tr>
			</table></div></div>
		<?php
		}
	}
	?>

	<?php if ($ticketData['status'] !== 'CLOSED') { ?>
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<form action="" method="post">
			<input type="hidden" name="username" value="<?php echo $ticketData['username']; ?>"><br>
			<div class="global-form__line"><textarea class="forumReply" name="reply_text" style="width: 610px; height: 150px;
    height: 220px;
    background: rgba(14, 9, 8, 0.9);
    border: 1px solid #2d1f1e;
    border-radius: 4px;
    padding: 10px 15px;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #d4c3b8;"></textarea></div><br>
			<div class="global-form__line"><input name="" type="submit" value="Post Reply" class="btn yellow flex-cc header__info-button"></div>
		</form>
	</div></div>
	<?php } ?>
	<?php
} else {

	$account = mysql_select_single("SELECT name,email FROM accounts WHERE id = $session_user_id");
	if (!empty($_POST)) {
		$required_fields = array('username', 'email', 'subject', 'message');
		foreach($_POST as $key=>$value) {
			if (empty($value) && in_array($key, $required_fields) === true) {
				$errors[] = 'You need to fill in all fields.';
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
			// Reversed this if, so: first check if you need to validate, then validate.
			if ($config['validate_IP'] === true && validate_ip(getIP()) === false) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
	}
	?>
	<div class="donate-page__title" style="color:white">Latests Tickets</div><br></br>
	<?php
	$tickets = mysql_select_multi("SELECT id,subject,creation,status FROM znote_tickets WHERE owner=$session_user_id ORDER BY creation DESC");
	if ($tickets !== false) {
		?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">
		<table>
			<tr class="yellow">
				<td><div class="donate-page__bonuses-title flex-cc">ID:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Subject:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Creation:</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Status:</div></td>
			</tr>
				<?php
				foreach ($tickets as $ticket) {
					echo '<tr class="special">';
						echo '<td><div class="donate-page__bonuses-title flex-cc">'. $ticket['id'] .'</div></td>';
						echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><a href="helpdesk.php?view='. $ticket['id'] .'">'. $ticket['subject'] .'</a></div></td>';
						echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">'. getClock($ticket['creation'], true) .'</div></td>';
						echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">'. $ticket['status'] .'</div></td>';
					echo '</tr>';
				}
				?>
		</table></div></div>
		<?php
	}
	?>
	<br></br>
	<?php
	if (isset($_GET['success']) && empty($_GET['success'])) {
		echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="color:lime">Congratulations! Your ticket has been created. We will reply up to 24 hours.</div></div>';
	} else {

		if (empty($_POST) === false && empty($errors) === true) {
			if ($config['log_ip']) {
				znote_visitor_insert_detailed_data(1);
			}

			//Save ticket on database
			$query = array(
				'owner'   =>	$session_user_id,
				'username'=>	getValue($_POST['username']),
				'subject' =>	getValue($_POST['subject']),
				'message' =>	getValue($_POST['message']),
				'ip'	  =>	getIPLong(),
				'creation' =>	time(),
				'status'  =>	'Open'
			);

			$fields = '`'. implode('`, `', array_keys($query)) .'`';
			$data = '\''. implode('\', \'', $query) .'\'';
			mysql_insert("INSERT INTO `znote_tickets` ($fields) VALUES ($data)");

			header('Location: helpdesk.php?success');
			exit();

		} else if (empty($errors) === false) {
			echo '<font color="red"><b>';
			echo output_errors($errors);
			echo '</b></font>';
		}
		?><center><div class="donate-page__title" style="color:white">Submit Ticket</div><br></br>
		<div class="donate-page__info">
		<div class="donate-page__content" id="donate_form">
		<div class="donate-page__title">Contact Us</div>
			<div class="donate-page__desc">Send a message to the support</div>
		<div class="global-form">
		<form action="" method="post">
			<ul>
			<div class="global-form__line">
			<div class="donate-page__desc">Account Name:</div><br>
					<input type="text" name="username" size="40" value="<?php echo $account['name']; ?>" style="color:white" disabled>
	</div>
	<div class="global-form__line">
	<div class="donate-page__desc">Email:</div><br>
					<input type="text" name="email" size="40" value="<?php echo $account['email']; ?>" style="color:white" disabled>
	</div>
	<div class="global-form__line">
	<div class="donate-page__desc">	Subject:</div><br>
					<input type="text" name="subject" size="40">
	</div>
	<div class="global-form__line">
	<div class="donate-page__desc">Message:</div><br>
					<textarea name="message" rows="7" cols="40" style="width: 100%;
    height: 220px;
    background: rgba(14, 9, 8, 0.9);
    border: 1px solid #2d1f1e;
    border-radius: 4px;
    padding: 10px 15px;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #d4c3b8;"></textarea>
	</div>
				<?php
				if ($config['use_captcha']) {
					?>
					<div class="global-form__line">
						 <div class="g-recaptcha" data-sitekey="<?php echo $config['captcha_site_key']; ?>"></div>
				</div>
					<?php
				}
				?>
				<?php
					/* Form file */
					Token::create();
				?>
				<div class="global-form__line">
					<input type="hidden" name="username" value="<?php echo $account['name']; ?>">
					<input type="submit" value="Submit ticket">
			</div>
			</ul>
		</form></div></div></div>
	</div></center>
		<?php
	}
}
include 'layout/overall/footer_login.php';
?>
