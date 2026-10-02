<?php require_once 'engine/init.php';
include 'layout/overall/header.php';

$shop = $config['shop'];
if ($shop['loginToView'] === true) protect_page();
$loggedin = user_logged_in();
$shop_list = $config['shop_offers'];
$purchase_msg = '';

if ($loggedin === true) {
	if (!empty($_POST['buy']) && $_SESSION['shop_session'] == $_POST['session']) {
		$time = time();
		$player_points = (int)$user_znote_data['points'];
		$cid = (int)$user_data['id'];
		$buy = false;
		$post = (int)$_POST['buy'];

		foreach ($shop_list as $key => $value) {
			if ($key === $post) $buy = $value;
		}
		if ($buy === false) die("Error: Shop offer ID mismatch.");

		if ($player_points >= $buy['points']) {
			$data = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$cid';");
			if (!$data) die("0: Account is not converted to work with Znote AAC");
			$old_points = $data['points'];
			if ((int)$old_points != (int)$player_points) die("1: Failed to equalize your points.");

			$expense_points = $buy['points'];
			$new_points = $old_points - $expense_points;
			mysql_update("UPDATE `znote_accounts` SET `points`='$new_points' WHERE `account_id`='$cid'");

			$data = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id`='$cid';");
			$verify = $data['points'];
			if ((int)$old_points == (int)$verify) die("2: Failed to equalize your points.");

			if ($buy['type'] == 5) {
				if (is_array($buy['itemid'])) {
					if (COUNT($buy['itemid']) == 2) $buy['itemid'] = ($buy['itemid'][0] * 1000) + $buy['itemid'][1];
					else $buy['itemid'] = $buy['itemid'][0];
				}
			}

			if ($buy['type'] == 2) {
				user_account_add_premdays($cid, $buy['count']);
				$purchase_msg = '<div class="cc-message success">You now have <strong>' . $buy['count'] . ' additional days</strong> of premium membership!</div>';
			} else if ($buy['type'] == 8) {
				mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
				$purchase_msg = '<div class="cc-message success"><strong>' . number_format($buy['count']) . ' Fame</strong> purchased! Use <code>!deliver</code> in-game to receive it.</div>';
			} else {
				mysql_insert("INSERT INTO `znote_shop_orders` (`account_id`, `type`, `itemid`, `count`, `time`) VALUES ('$cid', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '$time')");
				$purchase_msg = '<div class="cc-message success">Order ready! Use <code>!deliver</code> in-game to receive it.</div>';
			}

			mysql_insert("INSERT INTO `znote_shop_logs` (`account_id`, `player_id`, `type`, `itemid`, `count`, `points`, `time`) VALUES ('$cid', '0', '". $buy['type'] ."', '". $buy['itemid'] ."', '". $buy['count'] ."', '". $buy['points'] ."', '$time')");

			// Refresh points after purchase
			$user_znote_data['points'] = $new_points;
		} else {
			$purchase_msg = '<div class="cc-message error">Not enough points. This offer costs <strong>' . $buy['points'] . ' points</strong>. <a href="buypoints.php">Buy more points</a>.</div>';
		}
	}
}

if ($shop['enabled']):

// Categorize offers
$category_fame = array();
$category_premium = array();
foreach ($shop_list as $key => $offer) {
	$cat = isset($offer['category']) ? $offer['category'] : 'misc';
	if ($cat === 'fame') $category_fame[$key] = $offer;
	else if ($cat === 'premium') $category_premium[$key] = $offer;
}
?>

<h1 class="cd-page-title">Shop</h1>

<!-- Points balance -->
<?php if ($loggedin): ?>
<?php
	// Fetch account-wide fame data
	$fame_data = mysql_select_single("SELECT `level`, `points`, `total_points`, `spendable_points` FROM `player_fame` WHERE `account_id` = '" . (int)$session_user_id . "'");
	$fame_level = $fame_data ? (int)$fame_data['level'] : 0;
	$fame_points = $fame_data ? (int)$fame_data['points'] : 0;
	$fame_total = $fame_data ? (int)$fame_data['total_points'] : 0;
	$fame_spendable = $fame_data ? (int)$fame_data['spendable_points'] : 0;
?>
<div class="shop-stats">
	<div class="shop-stat-card">
		<span class="shop-stat-label">Shop Points</span>
		<span class="shop-stat-value"><?php echo number_format($user_znote_data['points']); ?></span>
		<a href="buypoints.php" class="shop-stat-link">+ Buy More</a>
	</div>
	<div class="shop-stat-card">
		<span class="shop-stat-label">Fame Level</span>
		<span class="shop-stat-value"><?php echo $fame_level; ?></span>
	</div>
	<div class="shop-stat-card">
		<span class="shop-stat-label">Total Fame</span>
		<span class="shop-stat-value"><?php echo number_format($fame_total); ?></span>
	</div>
	<div class="shop-stat-card">
		<span class="shop-stat-label">Spendable Fame</span>
		<span class="shop-stat-value"><?php echo number_format($fame_spendable); ?></span>
	</div>
</div>
<?php else: ?>
<div class="cc-message" style="margin-bottom:20px;">You need to <a href="login_1.php">log in</a> to use the shop.</div>
<?php endif; ?>

<?php echo $purchase_msg; ?>

<!-- ============ FAME SECTION ============ -->
<?php if (!empty($category_fame)): ?>
<div class="shop-section">
	<div class="shop-section-header">
		<img src="images/fame_logo.png" alt="Fame" class="shop-section-icon">
		<div>
			<h2 class="shop-section-title">Fame Packages</h2>
			<p class="shop-section-desc">Account-wide fame boost. Use <code>!deliver</code> in-game after purchase.</p>
		</div>
	</div>
	<div class="shop-grid">
		<?php foreach ($category_fame as $key => $offer): ?>
		<div class="shop-card">
			<div class="shop-card-img">
				<img src="images/fame_logo.png" alt="Fame">
			</div>
			<div class="shop-card-amount"><?php echo number_format($offer['count']); ?></div>
			<div class="shop-card-label">Fame Points</div>
			<div class="shop-card-price">
				<span class="shop-card-cost"><?php echo $offer['points']; ?></span> pts
			</div>
			<?php if ($loggedin): ?>
			<form action="" method="POST" onsubmit="return confirm('Buy <?php echo number_format($offer['count']); ?> Fame for <?php echo $offer['points']; ?> points?');">
				<input type="hidden" name="buy" value="<?php echo (int)$key; ?>">
				<input type="hidden" name="session" value="<?php echo time(); ?>">
				<button type="submit" class="shop-buy-btn">Purchase</button>
			</form>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<!-- ============ PREMIUM SECTION ============ -->
<?php if (!empty($category_premium)): ?>
<div class="shop-section">
	<div class="shop-section-header">
		<img src="images/premium_logo.png" alt="Premium" class="shop-section-icon">
		<div>
			<h2 class="shop-section-title">Premium</h2>
			<p class="shop-section-desc">Unlock exclusive features and benefits.</p>
		</div>
	</div>
	<div class="shop-grid">
		<?php foreach ($category_premium as $key => $offer): ?>
		<div class="shop-card shop-card-premium">
			<div class="shop-card-img">
				<img src="images/premium_logo.png" alt="Premium">
			</div>
			<div class="shop-card-amount"><?php echo $offer['count']; ?> Days</div>
			<div class="shop-card-label">Premium Account</div>
			<div class="shop-card-price">
				<span class="shop-card-cost"><?php echo $offer['points']; ?></span> pts
			</div>
			<?php if ($loggedin): ?>
			<form action="" method="POST" onsubmit="return confirm('Buy <?php echo $offer['count']; ?> days Premium for <?php echo $offer['points']; ?> points?');">
				<input type="hidden" name="buy" value="<?php echo (int)$key; ?>">
				<input type="hidden" name="session" value="<?php echo time(); ?>">
				<button type="submit" class="shop-buy-btn shop-buy-premium">Purchase</button>
			</form>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<?php
	$_SESSION['shop_session'] = time();
else:
	echo '<div class="cc-message error">Shop is currently disabled.</div>';
endif;
include 'layout/overall/footer_login.php'; ?>
