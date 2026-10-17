<?php
/**
 * Paystack Payment Gateway Integration
 * Healing My-Marriage International
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function paystack_enabled(): bool {
    return (bool)get_setting('paystack_enabled', true);
}

function paystack_mode(): string {
    return (string)get_setting('paystack_mode', 'test');
}

function paystack_public_key(): string {
    $val = trim((string)get_setting('paystack_public_key', ''));
    if (empty($val) && defined('ps_public') && !empty(ps_public)) {
        return ps_public;
    }
    return $val;
}

function paystack_secret_key(): string {
    $val = trim((string)get_setting('paystack_secret_key', ''));
    if (empty($val) && defined('ps_secret') && !empty(ps_secret)) {
        return ps_secret;
    }
    return $val;
}

function paystack_simulator_enabled(): bool {
    return (bool)get_setting('paystack_test_simulator', true);
}

function paystack_is_configured(): bool {
    $secret = paystack_secret_key();
    return !empty($secret) && (str_starts_with($secret, 'sk_test_') || str_starts_with($secret, 'sk_live_'));
}

/**
 * Initialize a Paystack transaction
 *
 * @param array $order Order data containing id, order_ref, customer_name, customer_email, customer_phone, total, items
 * @param string $callbackUrl URL to redirect user after payment
 * @return array ['status' => bool, 'authorization_url' => string, 'reference' => string, 'access_code' => string, 'message' => string]
 */
function paystack_initialize_transaction(array $order, string $callbackUrl): array {
    if (!paystack_enabled()) {
        return ['status' => false, 'message' => 'Paystack payment is currently disabled.'];
    }

    $secretKey = paystack_secret_key();
    $orderRef = $order['order_ref'];
    $amountKobo = (int)round($order['total'] * 100);

    // If real Paystack secret key is configured, call official API
    if (paystack_is_configured()) {
        $url = 'https://api.paystack.co/transaction/initialize';
        
        $metadata = [
            'order_id' => $order['id'] ?? null,
            'order_ref' => $orderRef,
            'user_id' => $order['user_id'] ?? null,
            'customer_name' => $order['customer_name'] ?? '',
            'customer_phone' => $order['customer_phone'] ?? '',
            'custom_fields' => [
                [
                    'display_name' => 'Order Reference',
                    'variable_name' => 'order_ref',
                    'value' => $orderRef
                ],
                [
                    'display_name' => 'Customer Name',
                    'variable_name' => 'customer_name',
                    'value' => $order['customer_name'] ?? ''
                ]
            ]
        ];

        $fields = [
            'email' => $order['customer_email'],
            'amount' => $amountKobo,
            'reference' => $orderRef,
            'currency' => 'NGN',
            'callback_url' => $callbackUrl,
            'metadata' => $metadata
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $secretKey,
                'Content-Type: application/json',
                'Cache-Control: no-cache'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            error_log('Paystack Init cURL Error: ' . $err);
            return ['status' => false, 'message' => 'Payment server connection timeout: ' . $err];
        }

        $result = json_decode((string)$response, true);
        if (!$result || empty($result['status']) || empty($result['data']['authorization_url'])) {
            $msg = $result['message'] ?? ('Paystack initialization failed (HTTP ' . $httpCode . ')');
            return ['status' => false, 'message' => $msg];
        }

        return [
            'status' => true,
            'authorization_url' => $result['data']['authorization_url'],
            'access_code' => $result['data']['access_code'] ?? '',
            'reference' => $result['data']['reference'] ?? $orderRef
        ];
    }

    // Fallback: If in test mode and keys not set yet, use the built-in development simulator
    if (paystack_mode() === 'test' && paystack_simulator_enabled()) {
        $simUrl = url('paystack_simulate.php?ref=' . urlencode($orderRef));
        return [
            'status' => true,
            'authorization_url' => $simUrl,
            'access_code' => 'SIM_' . bin2hex(random_bytes(8)),
            'reference' => $orderRef,
            'is_simulated' => true
        ];
    }

    return [
        'status' => false,
        'message' => 'Paystack API keys have not been configured. Please configure your Paystack keys in Admin Settings -> Payment.'
    ];
}

/**
 * Verify a Paystack transaction
 *
 * @param string $reference Transaction reference
 * @return array ['status' => bool, 'data' => array, 'message' => string]
 */
function paystack_verify_transaction(string $reference): array {
    $secretKey = paystack_secret_key();

    // Real Paystack verification
    if (paystack_is_configured()) {
        $url = 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $secretKey,
                'Cache-Control: no-cache'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            error_log('Paystack Verify cURL Error: ' . $err);
            return ['status' => false, 'message' => 'Payment verification network error: ' . $err];
        }

        $result = json_decode((string)$response, true);
        if (!$result || empty($result['status']) || empty($result['data'])) {
            $msg = $result['message'] ?? ('Verification failed (HTTP ' . $httpCode . ')');
            return ['status' => false, 'message' => $msg];
        }

        $data = $result['data'];
        if ($data['status'] === 'success') {
            return [
                'status' => true,
                'data' => $data,
                'amount' => $data['amount'] ?? 0,
                'channel' => $data['channel'] ?? 'card',
                'paid_at' => $data['paid_at'] ?? date('Y-m-d H:i:s')
            ];
        } else {
            return [
                'status' => false,
                'message' => $data['gateway_response'] ?? ($result['message'] ?? 'Payment was not successful.')
            ];
        }
    }

    // Simulated verification for test mode
    if (paystack_mode() === 'test' && paystack_simulator_enabled()) {
        $order = Database::fetchOne("SELECT * FROM orders WHERE order_ref = ? OR payment_reference = ?", [$reference, $reference]);
        if ($order) {
            return [
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'reference' => $reference,
                    'amount' => (int)($order['total'] * 100),
                    'channel' => 'paystack_test_simulator',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'customer' => ['email' => $order['customer_email']]
                ],
                'amount' => (int)($order['total'] * 100),
                'channel' => 'paystack_test_simulator',
                'paid_at' => date('Y-m-d H:i:s')
            ];
        }
    }

    return ['status' => false, 'message' => 'Paystack secret key is not configured.'];
}

/**
 * Verify Paystack webhook signature
 *
 * @param string $payload Raw request body
 * @param string|null $signature Value of HTTP_X_PAYSTACK_SIGNATURE header
 * @return bool
 */
function paystack_verify_webhook(string $payload, ?string $signature): bool {
    if (empty($signature)) return false;
    $secret = paystack_secret_key();
    if (empty($secret)) return false;
    $computed = hash_hmac('sha512', $payload, $secret);
    return hash_equals($computed, $signature);
}
