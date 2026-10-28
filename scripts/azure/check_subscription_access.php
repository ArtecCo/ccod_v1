<?php

/**
 * Check direct ARM access to specific subscriptions using the configured
 * CCOD Azure service principal.
 *
 * Populate SUBSCRIPTION_IDS below with the subscription IDs that the service
 * principal was granted Reader access to, then run:
 *
 *   php scripts/azure/check_subscription_access.php
 *
 * This script never prints the client secret or bearer token.
 */

declare(strict_types=1);

require_once __DIR__ . '/azure_client.php';

// Replace these placeholders with the actual subscription IDs.
$subscriptionIds = [
    'SUBSCRIPTION_ID_1',
    'SUBSCRIPTION_ID_2',
];

$subscriptionIds = array_values(array_filter(
    array_map('trim', $subscriptionIds),
    static fn (string $id): bool => $id !== '' && ! str_starts_with($id, 'SUBSCRIPTION_ID_'),
));

if ($subscriptionIds === []) {
    fwrite(STDERR, "No subscription IDs configured. Edit scripts/azure/check_subscription_access.php and populate \$subscriptionIds.\n");
    exit(1);
}

try {
    echo "CCOD Azure subscription access test\n";
    echo "==================================\n\n";
    echo "Requesting Microsoft Entra access token... ";

    $token = azureAccessToken();
    echo "OK\n\n";

    foreach ($subscriptionIds as $subscriptionId) {
        echo "Subscription: {$subscriptionId}\n";
        echo str_repeat('-', 72) . "\n";

        $url = 'https://management.azure.com/subscriptions/'
            . rawurlencode($subscriptionId)
            . '?api-version=2020-01-01';

        $response = azureGet($url, $token);

        echo "HTTP status : {$response['status']}\n";

        if ($response['status'] >= 200 && $response['status'] < 300) {
            $body = $response['body'];

            echo "Access      : YES\n";
            echo 'Response keys: ' . implode(', ', array_keys($body)) . "\n";
            echo 'Subscription ID: ' . ($body['subscriptionId'] ?? '—') . "\n";
            echo 'Display name: ' . ($body['displayName'] ?? '—') . "\n";
            echo 'State       : ' . ($body['state'] ?? '—') . "\n";
            echo 'Tenant ID   : ' . ($body['tenantId'] ?? '—') . "\n";
            echo 'Authorization source: ' . ($body['authorizationSource'] ?? '—') . "\n";

            // Print the non-sensitive ARM response so we can diagnose an
            // unexpected response shape without exposing the bearer token.
            echo "Response body:\n";
            echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        } else {
            echo "Access      : NO / NOT CONFIRMED\n";
            azurePrintError($response);
        }

        echo "\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Test failed: {$e->getMessage()}\n");
    exit(1);
}
