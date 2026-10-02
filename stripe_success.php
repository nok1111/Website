<?php
/**
 * Stripe - Success Page
 * User is redirected here after completing Stripe checkout.
 * Verifies the session, credits points if not already credited.
 */
require_once 'engine/init.php';

if (!user_logged_in()) {
    header('Location: login_1.php');
    exit;
}

$session_id = isset($_GET['session_id']) ? trim($_GET['session_id']) : '';

if (empty($session_id)) {
    header('Location: buypoints.php');
    exit;
}

$stripe = $config['stripe'];
$prices = isset($config['stripe_prices']) ? $config['stripe_prices'] : $config['paypal_prices'];

// Retrieve the Checkout Session from Stripe API
$ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . urlencode($session_id) . '?expand[]=payment_intent');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $stripe['secret_key'] . ':');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/engine/cert/cacert.pem');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$success = false;
$points_added = 0;
$error_msg = '';

if ($http_code === 200) {
    $session = json_decode($response, true);

    if ($session['payment_status'] === 'paid') {
        $user_id = isset($session['metadata']['user_id']) ? (int)$session['metadata']['user_id'] : 0;
        $price = isset($session['metadata']['price']) ? (int)$session['metadata']['price'] : 0;
        $points = isset($session['metadata']['points']) ? (int)$session['metadata']['points'] : 0;
        $txn_id = isset($session['payment_intent']['id']) ? $session['payment_intent']['id'] : $session_id;

        // Verify user matches
        if ($user_id !== (int)$session_user_id) {
            $error_msg = 'User mismatch.';
        }
        // Verify price tier
        else if (!isset($prices[$price]) || $prices[$price] !== $points) {
            $error_msg = 'Invalid price tier.';
        }
        // Check not already processed
        else {
            $txn_check = mysql_select_single("SELECT `id` FROM `znote_paypal` WHERE `txn_id` = '" . mysql_znote_escape_string($txn_id) . "'");
            if ($txn_check !== false) {
                // Already processed — still show success
                $success = true;
                $points_added = $points;
            } else {
                // Credit points
                $payer_email = isset($session['customer_details']['email']) ? mysql_znote_escape_string($session['customer_details']['email']) : 'stripe';

                mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '" . mysql_znote_escape_string($txn_id) . "', '$payer_email', '$user_id', '$price', '$points')");

                $data = mysql_select_single("SELECT `points` AS `old_points` FROM `znote_accounts` WHERE `account_id` = '$user_id'");
                $new_points = $data['old_points'] + $points;
                mysql_update("UPDATE `znote_accounts` SET `points` = '$new_points' WHERE `account_id` = '$user_id'");

                $success = true;
                $points_added = $points;
            }
        }
    } else {
        $error_msg = 'Payment not completed.';
    }
} else {
    $error_msg = 'Could not verify payment.';
}

include 'layout/overall/header.php';
?>

<h1 class="cd-page-title"><?php echo $success ? 'Payment Complete!' : 'Payment Issue'; ?></h1>

<?php if ($success): ?>
<div class="cc-message success">
    <strong>Thank you!</strong> You received <strong><?php echo number_format($points_added); ?> points</strong>. They have been added to your account.<br>
    <a href="shop.php">Visit the Shop</a> &middot; <a href="buypoints.php">Buy More Points</a> &middot; <a href="myaccount.php">My Account</a>
</div>
<?php else: ?>
<div class="cc-message error">
    <?php echo htmlspecialchars($error_msg); ?><br>
    <a href="buypoints.php">Try again</a> &middot; <a href="helpdesk.php">Contact Support</a>
</div>
<?php endif; ?>

<?php include 'layout/overall/footer_login.php'; ?>
