<?php
/**
 * Stripe - Create Checkout Session
 * Called via AJAX from buypoints.php when user clicks a Stripe price tier.
 * Redirects user to Stripe's hosted checkout page.
 */
header('Content-Type: application/json');

require_once 'engine/init.php';

// Must be logged in
if (!user_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$price = isset($input['price']) ? (int)$input['price'] : 0;

$stripe = $config['stripe'];
$prices = isset($config['stripe_prices']) ? $config['stripe_prices'] : $config['paypal_prices'];

// Validate price tier
if (!isset($prices[$price])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid price tier']);
    exit;
}

$points = $prices[$price];
$currency = strtolower($stripe['currency']);

// Build Stripe Checkout Session via API
$session_data = http_build_query([
    'payment_method_types[]' => 'card',
    'line_items[0][price_data][currency]' => $currency,
    'line_items[0][price_data][product_data][name]' => $points . ' Shop Points',
    'line_items[0][price_data][product_data][description]' => $points . ' shop points on ' . $config['site_title'],
    'line_items[0][price_data][unit_amount]' => $price * 100, // Stripe uses cents
    'line_items[0][quantity]' => 1,
    'mode' => 'payment',
    'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/stripe_success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/buypoints.php',
    'metadata[user_id]' => $session_user_id,
    'metadata[price]' => $price,
    'metadata[points]' => $points,
    'client_reference_id' => $session_user_id,
]);

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $session_data);
curl_setopt($ch, CURLOPT_USERPWD, $stripe['secret_key'] . ':');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/engine/cert/cacert.pem');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code >= 200 && $http_code < 300) {
    $session = json_decode($response, true);
    echo json_encode(['url' => $session['url']]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create Stripe session', 'details' => $response]);
}
