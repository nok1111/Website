<?php
/**
 * Stripe Webhook Endpoint
 * Receives events from Stripe (e.g. checkout.session.completed)
 * Credits points as a backup if stripe_success.php didn't process it.
 * Configure this URL in Stripe Dashboard > Webhooks:
 *   https://yourdomain.com/stripe_webhook.php
 */
require 'config.php';
require 'engine/database/connect.php';

// Read raw body
$payload = file_get_contents('php://input');
$sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';

$stripe = $config['stripe'];
$prices = isset($config['stripe_prices']) ? $config['stripe_prices'] : $config['paypal_prices'];

// Optional: verify webhook signature
if (!empty($stripe['webhook_secret']) && !empty($sig_header)) {
    if (!verify_stripe_signature($payload, $sig_header, $stripe['webhook_secret'])) {
        http_response_code(400);
        exit('Invalid signature');
    }
}

$event = json_decode($payload, true);

if (!$event || !isset($event['type'])) {
    http_response_code(400);
    exit('Invalid payload');
}

// Only process checkout.session.completed
if ($event['type'] === 'checkout.session.completed') {
    $session = $event['data']['object'];

    if ($session['payment_status'] === 'paid') {
        $user_id = isset($session['metadata']['user_id']) ? (int)$session['metadata']['user_id'] : 0;
        $price = isset($session['metadata']['price']) ? (int)$session['metadata']['price'] : 0;
        $points = isset($session['metadata']['points']) ? (int)$session['metadata']['points'] : 0;
        $txn_id = isset($session['payment_intent']) ? $session['payment_intent'] : $session['id'];

        // Validate
        if ($user_id > 0 && isset($prices[$price]) && $prices[$price] === $points) {
            // Check not already processed
            $escaped_txn = mysql_znote_escape_string($txn_id);
            $txn_check = mysql_select_single("SELECT `id` FROM `znote_paypal` WHERE `txn_id` = '$escaped_txn'");

            if ($txn_check === false) {
                $payer_email = isset($session['customer_details']['email']) ? mysql_znote_escape_string($session['customer_details']['email']) : 'stripe-webhook';

                mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '$escaped_txn', '$payer_email', '$user_id', '$price', '$points')");

                $data = mysql_select_single("SELECT `points` AS `old_points` FROM `znote_accounts` WHERE `account_id` = '$user_id'");
                $new_points = $data['old_points'] + $points;
                mysql_update("UPDATE `znote_accounts` SET `points` = '$new_points' WHERE `account_id` = '$user_id'");
            }
        }
    }
}

http_response_code(200);
echo json_encode(['received' => true]);

/**
 * Verify Stripe webhook signature (HMAC SHA256)
 */
function verify_stripe_signature($payload, $sig_header, $secret) {
    $elements = explode(',', $sig_header);
    $timestamp = null;
    $signatures = [];

    foreach ($elements as $element) {
        $parts = explode('=', $element, 2);
        if ($parts[0] === 't') {
            $timestamp = $parts[1];
        } elseif ($parts[0] === 'v1') {
            $signatures[] = $parts[1];
        }
    }

    if (!$timestamp || empty($signatures)) {
        return false;
    }

    // Tolerance: reject events older than 5 minutes
    if (abs(time() - $timestamp) > 300) {
        return false;
    }

    $signed_payload = $timestamp . '.' . $payload;
    $expected = hash_hmac('sha256', $signed_payload, $secret);

    foreach ($signatures as $sig) {
        if (hash_equals($expected, $sig)) {
            return true;
        }
    }
    return false;
}
