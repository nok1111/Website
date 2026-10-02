<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

// Declare as int
$view = (isset($_GET['view']) && (int)$_GET['view'] > 0) ? (int)$_GET['view'] : false;
if ($view !== false){
	if (!empty($_POST['reply_text'])) {
		sanitize($_POST['reply_text']);

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
		mysql_update("UPDATE `znote_tickets` SET `status`='Staff-Reply' WHERE `id`='$view' LIMIT 1;");

	} else if (!empty($_POST['admin_ticket_close'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_update("UPDATE `znote_tickets` SET `status` = 'CLOSED' WHERE `id` ='$ticketId' LIMIT 1;");

	} else if (!empty($_POST['admin_ticket_open'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_update("UPDATE `znote_tickets` SET `status` = 'Open' WHERE `id` ='$ticketId' LIMIT 1;");

	} else if (!empty($_POST['admin_ticket_delete'])) {
		$ticketId = (int) $_POST['admin_ticket_id'];
		mysql_delete("DELETE FROM `znote_tickets` WHERE `id`='$ticketId' LIMIT 1;");
		header("Location: admin_helpdesk.php");
	}

	$ticketData = mysql_select_single("SELECT * FROM znote_tickets WHERE id='$view' LIMIT 1;");
	if(!$ticketData) {
		echo 'You can not view this ticket!';
		include 'layout/overall/footer.php';
		die;
	}
	?>
	<h1>View Ticket #<?php echo $ticketData['id']; ?></h1>
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

	<!-- Open/Close Ticket -->
	<table class="znoteTable ThreadTable table table-striped">
		<tr>
			<td>
				<form action="" method="post" align="center">
					<input type="hidden" name="admin_ticket_id" value="<?php echo $ticketData['id']; ?>">
				<?php if ($ticketData['status'] !== 'CLOSED') { ?>
					<button type="submit" name="admin_ticket_close" value="Close Ticket" class="btn yellow flex-cc header__info-button">Close Ticket</button>
				<?php } else { ?>
					<button type="submit" name="admin_ticket_open" value="Open Ticket" class="btn yellow flex-cc header__info-button">Open Ticket</button>
				<?php } ?>
				</form>
			</td>
			<td>
				<form action="" method="post" align="center" onClick="return confirm('Are you sure you want to delete this ticket?');">
					<input type="hidden" name="admin_ticket_id" value="<?php echo $ticketData['id']; ?>">
					<button type="submit" name="admin_ticket_delete" value="Delete Ticket" class="btn yellow flex-cc header__info-button">Delete Ticket</button>
				</form>
			</td>
		</tr>
	</table>

	<?php if ($ticketData['status'] !== 'CLOSED') { ?>
		
		<form action="" method="post">
			<input type="hidden" name="username" value="ADMIN"><br>
			<textarea class="forumReply" name="reply_text" style="width: 610px; height: 150px;
    height: 220px;
    background: rgba(14, 9, 8, 0.9);
    border: 1px solid #2d1f1e;
    border-radius: 4px;
    padding: 10px 15px;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #d4c3b8;"></textarea><br>
			<button name="" type="submit" value="Post Reply" class="btn yellow flex-cc header__info-button">Post Reply</button>
		</form>
	<?php } ?>
	<?php
} else {
	?>
	<div class="donate-page__title" style="color:white">Latests Tickets</div><br></br>
	<?php
	$tickets = mysql_select_multi("SELECT id,subject,creation,status FROM znote_tickets ORDER BY creation DESC");
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
						echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><a href="admin_helpdesk.php?view='. $ticket['id'] .'">'. $ticket['subject'] .'</a></div></td>';
						echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">'. getClock($ticket['creation'], true) .'</div></td>';
						echo '<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">'. $ticket['status'] .'</div></td>';
					echo '</tr>';
				}
				?>
		</table></div></div>
		<?php
	} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">No helpdesk tickets has been submitted.</div></div>';
}
include 'layout/overall/footer_login.php';
?>
