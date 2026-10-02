<?php
/**
 * PayPal REST API - Create Order
 * Called via AJAX from buypoints.php when user clicks a price tier.
 * Returns the PayPal order ID for the JS SDK to approve.
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
$price = isset($input['price']) ? (int)$input['price'] : 0;

$paypal = $config['paypal'];
$prices = $config['paypal_prices'];

// Validate price tier exists
if (!isset($prices[$price])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid price tier']);
    exit;
}

$points = $prices[$price];
$currency = $paypal['currency'];

// Get PayPal access token
$access_token = paypal_get_access_token($paypal);
if (!$access_token) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to authenticate with PayPal']);
    exit;
}

// Create order via PayPal REST API
$base_url = $paypal['sandbox'] ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

$order_data = [
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => [
            'currency_code' => $currency,
            'value' => number_format($price, 2, '.', '')
        ],
        'description' => $points . ' shop points on ' . $config['site_title'],
        'custom_id' => $session_user_id . '|' . $price . '|' . $points
    ]]
];

$ch = curl_init($base_url . '/v2/checkout/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
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

if ($http_code >= 200 && $http_code < 300) {
    $order = json_decode($response, true);
    echo json_encode(['id' => $order['id']]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create PayPal order', 'details' => $response]);
}

/**
 * Get PayPal OAuth2 access token using client credentials
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
