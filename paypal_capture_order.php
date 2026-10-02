<?php
/**
 * PayPal REST API - Capture Order
 * Called via AJAX after user approves payment in PayPal popup.
 * Captures the payment and credits points to the user's account.
 */
header('Content-Type: application/json');

require_once 'engine/init.php';

// Must be logged in
if (!user_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Read JSON body
$input = json_decode(file_get_contents('php://input'), true);
$order_id = isset($input['order_id']) ? trim($input['order_id']) : '';

if (empty($order_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}

$paypal = $config['paypal'];
$prices = $config['paypal_prices'];

// Get PayPal access token
$access_token = paypal_get_access_token($paypal);
if (!$access_token) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to authenticate with PayPal']);
    exit;
}

$base_url = $paypal['sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

// Capture the order
$ch = curl_init($base_url . '/v2/checkout/orders/' . $order_id . '/capture');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token,
    'Prefer: return=representation'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/engine/cert/cacert.pem');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);

// Verify capture was successful
if ($http_code < 200 || $http_code >= 300 || !isset($order['status']) || $order['status'] !== 'COMPLETED') {
    http_response_code(500);
    $log_msg = 'Capture failed. HTTP ' . $http_code . ' - ' . substr($response, 0, 500);
    paypal_log_transaction('CAPTURE_FAIL', $log_msg, $session_user_id, 0, 0);
    echo json_encode(['error' => 'Payment capture failed']);
    exit;
}

// Extract payment details from captured order
$capture = $order['purchase_units'][0]['payments']['captures'][0];
$txn_id = $capture['id'];
$payment_amount = $capture['amount']['value'];
$payment_currency = $capture['amount']['currency_code'];
$custom_id = isset($order['purchase_units'][0]['custom_id']) ? $order['purchase_units'][0]['custom_id'] : '';

// Parse custom_id: "user_id|price|points"
$custom_parts = explode('|', $custom_id);
$expected_user_id = isset($custom_parts[0]) ? (int)$custom_parts[0] : 0;
$expected_price = isset($custom_parts[1]) ? (int)$custom_parts[1] : 0;
$expected_points = isset($custom_parts[2]) ? (int)$custom_parts[2] : 0;

// Security validations
$errors = [];

// 1. Check user matches
if ($expected_user_id !== (int)$session_user_id) {
    $errors[] = 'User mismatch';
}

// 2. Check currency
if (strtoupper($payment_currency) !== strtoupper($paypal['currency'])) {
    $errors[] = 'Currency mismatch: ' . $payment_currency;
}

// 3. Check amount matches a valid price tier
$paid_price = (int)round((float)$payment_amount);
if (!isset($prices[$paid_price])) {
    // Try exact float match
    $found = false;
    foreach ($prices as $p => $pts) {
        if (abs((float)$payment_amount - (float)$p) < 0.01) {
            $paid_price = $p;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $errors[] = 'Invalid amount: ' . $payment_amount;
    }
}

// 4. Check txn_id not already processed
$txn_check = mysql_select_single("SELECT `id` FROM `znote_paypal` WHERE `txn_id` = '" . mysql_znote_escape_string($txn_id) . "'");
if ($txn_check !== false) {
    $errors[] = 'Duplicate transaction: ' . $txn_id;
}

if (!empty($errors)) {
    $error_msg = implode('; ', $errors);
    paypal_log_transaction($txn_id, 'ERROR: ' . $error_msg, $session_user_id, $payment_amount, 0);
    http_response_code(400);
    echo json_encode(['error' => 'Payment validation failed']);
    exit;
}

// All checks passed — credit points
$points_to_add = $prices[$paid_price];
$payer_email = isset($order['payer']['email_address']) ? mysql_znote_escape_string($order['payer']['email_address']) : 'unknown';

// Log transaction
paypal_log_transaction($txn_id, $payer_email, $session_user_id, $paid_price, $points_to_add);

// Add points to user account
$data = mysql_select_single("SELECT `points` AS `old_points` FROM `znote_accounts` WHERE `account_id` = '" . (int)$session_user_id . "'");
$new_points = $data['old_points'] + $points_to_add;
mysql_update("UPDATE `znote_accounts` SET `points` = '" . $new_points . "' WHERE `account_id` = '" . (int)$session_user_id . "'");

echo json_encode([
    'success' => true,
    'points' => $points_to_add,
    'total_points' => $new_points,
    'txn_id' => $txn_id
]);

/**
 * Log a PayPal transaction to znote_paypal table
 */
function paypal_log_transaction($txn_id, $payer_email, $user_id, $amount, $points) {
    $txn_id = mysql_znote_escape_string($txn_id);
    $payer_email = mysql_znote_escape_string($payer_email);
    $user_id = (int)$user_id;
    $amount = (int)$amount;
    $points = (int)$points;
    mysql_insert("INSERT INTO `znote_paypal` VALUES ('0', '$txn_id', '$payer_email', '$user_id', '$amount', '$points')");
}

/**
 * Get PayPal OAuth2 access token
 */
function paypal_get_access_token($paypal) {
    $base_url = $paypal['sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

    $ch = curl_init($base_url . '/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_USERPWD, $paypal['client_id'] . ':' . $paypal['client_secret']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/engine/cert/cacert.pem');

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);
        return $data['access_token'];
    }
    return false;
}
