<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

$pagseguro = $config['pagseguro'];
$paypal = $config['paypal'];
$stripe = $config['stripe'];
$prices = $config['paypal_prices'];
$stripe_prices = isset($config['stripe_prices']) ? $config['stripe_prices'] : $prices;

// Current user points
$user_points = mysql_select_single("SELECT `points` FROM `znote_accounts` WHERE `account_id` = '" . (int)$session_user_id . "'");
$current_points = isset($user_points['points']) ? (int)$user_points['points'] : 0;

$has_paypal = $paypal['enabled'];
$has_stripe = $stripe['enabled'];
?>

<h1 class="cd-page-title">Donate & Get Points</h1>

<div class="donate-balance">
	<span class="donate-balance-label">Your Balance:</span>
	<span class="donate-balance-value" id="user-points"><?php echo number_format($current_points); ?></span>
	<span class="donate-balance-unit">points</span>
</div>

<?php if ($has_paypal || $has_stripe): ?>

<!-- Payment method tabs -->
<div class="donate-tabs">
	<?php if ($has_paypal): ?>
	<button class="donate-tab active" data-tab="paypal" onclick="switchTab('paypal')">
		<i class="fa fa-paypal"></i> PayPal
	</button>
	<?php endif; ?>
	<?php if ($has_stripe): ?>
	<button class="donate-tab<?php echo !$has_paypal ? ' active' : ''; ?>" data-tab="stripe" onclick="switchTab('stripe')">
		<i class="fa fa-credit-card"></i> Card / Stripe
	</button>
	<?php endif; ?>
</div>

<!-- ============ PAYPAL TAB ============ -->
<?php if ($has_paypal): ?>
<div class="donate-tab-content" id="tab-paypal" style="display:block;">
	<div class="donate-grid">
	<?php foreach ($prices as $price => $points):
		$base_points = $paypal['points_per_currency'] * $price;
		$bonus = $paypal['showBonus'] ? calculate_discount($base_points, $points) : '';
	?>
		<div class="donate-card">
			<div class="donate-card-price"><?php echo $price; ?> <small><?php echo $paypal['currency']; ?></small></div>
			<div class="donate-card-points"><?php echo number_format($points); ?> points</div>
			<?php if ($bonus && $bonus !== '0%'): ?>
			<div class="donate-card-bonus"><?php echo $bonus; ?> bonus</div>
			<?php endif; ?>
			<div class="donate-card-paypal" id="paypal-btn-<?php echo $price; ?>"></div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<!-- ============ STRIPE TAB ============ -->
<?php if ($has_stripe): ?>
<div class="donate-tab-content" id="tab-stripe" style="display:<?php echo !$has_paypal ? 'block' : 'none'; ?>;">
	<div class="donate-grid">
	<?php foreach ($stripe_prices as $price => $points):
		$base_points = $stripe['points_per_currency'] * $price;
		$bonus = $stripe['showBonus'] ? calculate_discount($base_points, $points) : '';
	?>
		<div class="donate-card">
			<div class="donate-card-price"><?php echo $price; ?> <small><?php echo $stripe['currency']; ?></small></div>
			<div class="donate-card-points"><?php echo number_format($points); ?> points</div>
			<?php if ($bonus && $bonus !== '0%'): ?>
			<div class="donate-card-bonus"><?php echo $bonus; ?> bonus</div>
			<?php endif; ?>
			<button class="donate-stripe-btn" onclick="stripeCheckout(<?php echo $price; ?>, this)">
				<i class="fa fa-credit-card"></i> Pay <?php echo $price; ?> <?php echo $stripe['currency']; ?>
			</button>
		</div>
	<?php endforeach; ?>
	</div>
	<div class="donate-stripe-methods">
		Accepts: Visa, Mastercard, Amex, Apple Pay, Google Pay, iDEAL, SEPA, PIX & more
	</div>
</div>
<?php endif; ?>

<!-- Success/Error overlay -->
<div class="donate-overlay" id="donate-overlay" style="display:none;">
	<div class="donate-overlay-card">
		<div class="donate-overlay-icon" id="donate-overlay-icon"></div>
		<div class="donate-overlay-title" id="donate-overlay-title"></div>
		<div class="donate-overlay-text" id="donate-overlay-text"></div>
		<button class="cd-btn-hero" onclick="document.getElementById('donate-overlay').style.display='none'">OK</button>
	</div>
</div>

<script>
// Tab switching
function switchTab(tab) {
	document.querySelectorAll('.donate-tab').forEach(function(t) { t.classList.remove('active'); });
	document.querySelectorAll('.donate-tab-content').forEach(function(c) { c.style.display = 'none'; });
	document.querySelector('.donate-tab[data-tab="'+tab+'"]').classList.add('active');
	document.getElementById('tab-' + tab).style.display = 'block';
}

function showOverlay(type, title, text) {
	var o = document.getElementById('donate-overlay');
	document.getElementById('donate-overlay-icon').innerHTML = type === 'success' ? '&#10003;' : '&#10007;';
	document.getElementById('donate-overlay-icon').className = 'donate-overlay-icon ' + type;
	document.getElementById('donate-overlay-title').textContent = title;
	document.getElementById('donate-overlay-text').textContent = text;
	o.style.display = 'flex';
}

// Stripe checkout
function stripeCheckout(price, btn) {
	btn.disabled = true;
	btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Redirecting...';
	fetch('stripe_create_checkout.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ price: price })
	})
	.then(function(res) { return res.json(); })
	.then(function(data) {
		if (data.url) {
			window.location.href = data.url;
		} else {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-credit-card"></i> Pay ' + price + ' <?php echo $stripe['currency']; ?>';
			showOverlay('error', 'Error', data.error || 'Failed to start checkout.');
		}
	})
	.catch(function(err) {
		btn.disabled = false;
		btn.innerHTML = '<i class="fa fa-credit-card"></i> Pay ' + price + ' <?php echo $stripe['currency']; ?>';
		showOverlay('error', 'Error', err.message || 'Network error.');
	});
}
</script>

<?php if ($has_paypal): ?>
<!-- PayPal JS SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo hhb_tohtml($paypal['client_id']); ?>&currency=<?php echo hhb_tohtml($paypal['currency']); ?>&intent=capture&locale=en_US"></script>
<script>
<?php foreach ($prices as $price => $points): ?>
paypal.Buttons({
	style: { layout:'horizontal', color:'gold', shape:'rect', label:'pay', height:40, tagline:false },
	createOrder: function() {
		return fetch('paypal_create_order.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ price: <?php echo $price; ?> })
		}).then(function(r){return r.json()}).then(function(d){ if(d.error) throw new Error(d.error); return d.id; });
	},
	onApprove: function(data) {
		return fetch('paypal_capture_order.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ order_id: data.orderID })
		}).then(function(r){return r.json()}).then(function(result) {
			if (result.success) {
				document.getElementById('user-points').textContent = result.total_points.toLocaleString();
				showOverlay('success', 'Payment Complete!', 'You received ' + result.points + ' points. Transaction: ' + result.txn_id);
			} else { showOverlay('error', 'Payment Failed', result.error || 'Something went wrong.'); }
		}).catch(function(err){ showOverlay('error', 'Error', err.message || 'Capture failed.'); });
	},
	onError: function(err) { showOverlay('error', 'PayPal Error', 'There was a problem processing your payment.'); }
}).render('#paypal-btn-<?php echo $price; ?>');
<?php endforeach; ?>
</script>
<?php endif; ?>

<?php endif; ?>

<?php if ($config['pagseguro']['enabled'] == true): ?>
<h2 style="margin-top:40px;" class="cd-page-title">Pagseguro</h2>
<form target="pagseguro" action="https://<?php echo hhb_tohtml($pagseguro['urls']['www']); ?>/checkout/checkout.jhtml" method="post">
	<input type="hidden" name="email_cobranca" value="<?php echo hhb_tohtml($pagseguro['email']); ?>">
	<input type="hidden" name="tipo" value="CP">
	<input type="hidden" name="moeda" value="<?php echo hhb_tohtml($pagseguro['currency']); ?>">
	<input type="hidden" name="ref_transacao" value="<?php echo (int)$session_user_id; ?>">
	<input type="hidden" name="item_id_1" value="1">
	<input type="hidden" name="item_descr_1" value="<?php echo hhb_tohtml($pagseguro['product_name']); ?>">
	<input type="number" name="item_quant_1" min="1" step="4" value="1">
	<input type="hidden" name="item_peso_1" value="0">
	<input type="hidden" name="item_valor_1" value="<?php echo $pagseguro['price']; ?>">
	<input type="submit" value="Purchase">
</form>
<?php endif; ?>

<?php if ($config['paygol']['enabled'] == true):
	$paygol = $config['paygol'];
?>
<h2 style="margin-top:40px;" class="cd-page-title">PayGol</h2>
<p style="color:#888;margin-bottom:12px;"><?php echo $paygol['price'] . ' ' . hhb_tohtml($paygol['currency']) . ' for ' . $paygol['points'] . ' points'; ?></p>
<form name="pg_frm" method="post" action="http://www.paygol.com/micropayment/paynow">
	<input type="hidden" name="pg_serviceid" value="<?php echo hhb_tohtml($paygol['serviceID']); ?>">
	<input type="hidden" name="pg_currency" value="<?php echo hhb_tohtml($paygol['currency']); ?>">
	<input type="hidden" name="pg_name" value="<?php echo hhb_tohtml($paygol['name']); ?>">
	<input type="hidden" name="pg_custom" value="<?php echo hhb_tohtml($session_user_id); ?>">
	<input type="hidden" name="pg_price" value="<?php echo $paygol['price']; ?>">
	<input type="hidden" name="pg_return_url" value="<?php echo hhb_tohtml($paygol['returnURL']); ?>">
	<input type="hidden" name="pg_cancel_url" value="<?php echo hhb_tohtml($paygol['cancelURL']); ?>">
	<input type="image" name="pg_button" src="https://www.paygol.com/micropayment/img/buttons/150/black_en_pbm.png" border="0" alt="PayGol" title="PayGol">
</form>
<?php endif; ?>

<?php
if (!$has_paypal && !$has_stripe && !$config['paygol']['enabled'] && !$config['pagseguro']['enabled'])
	echo '<div class="cc-message error">Buy Points system is currently disabled.</div>';
include 'layout/overall/footer_login.php'; ?>
